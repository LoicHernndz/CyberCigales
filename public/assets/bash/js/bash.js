/**
 * Terminal SSH simulé pour l'escape game CyberCigales
 *
 * Flux :
 *  1. Machine locale  → le joueur tape "ssh user@host"
 *  2. Saisie du mot de passe (masqué)
 *  3. Vérification côté serveur
 *  4. Si OK → accès au filesystem du hacker (commandes ls, cd, cat…)
 */

const output   = document.getElementById("output");
const input    = document.getElementById("input");
const promptEl = document.getElementById("prompt");
const wrapper  = document.getElementById("terminal-wrapper");

// ── État du terminal ──────────────────────────────────────────────
let state       = "local";        // "local" | "password" | "connected"
let sshUser     = "";
let sshHost     = "";
let path        = "/home";
let attempts    = 0;
const MAX_ATTEMPTS = 3;

// ── Initialisation ────────────────────────────────────────────────
updatePrompt();
appendLine("Bienvenue sur le terminal local.");
appendLine("Tapez <span class='cmd-highlight'>help</span> pour la liste des commandes.\n");

// Focus auto sur l'input au clic n'importe où
wrapper.addEventListener("click", () => input.focus());
input.focus();

// ── Gestion des touches ───────────────────────────────────────────
input.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
        e.preventDefault();
        const value = input.value;
        input.value = "";

        if (state === "password") {
            handlePasswordSubmit(value);
        } else {
            // Afficher la commande tapée
            appendLine(promptText() + value);
            handleCommand(value.trim());
        }
    }
});

// ── Prompt ────────────────────────────────────────────────────────
function promptText() {
    if (state === "local") {
        return '<span class="prompt-user">guest@local</span>:<span class="prompt-path">~</span>$ ';
    }
    if (state === "password") {
        return sshUser + "@" + sshHost + "'s password: ";
    }
    // connected
    const dir = path.split("/").pop();
    return '<span class="prompt-user">' + sshUser + '@' + sshHost + '</span>:<span class="prompt-path">' + dir + '</span>$ ';
}

function updatePrompt() {
    promptEl.innerHTML = promptText();
    if (state === "password") {
        input.type = "password";
    } else {
        input.type = "text";
    }
}

// ── Affichage ─────────────────────────────────────────────────────
function appendLine(html) {
    output.innerHTML += html + "<br>";
    scrollToBottom();
}

function scrollToBottom() {
    wrapper.scrollTop = wrapper.scrollHeight;
}

// ── Commande Router ───────────────────────────────────────────────
function handleCommand(cmd) {
    if (cmd === "") {
        updatePrompt();
        return;
    }

    if (state === "local") {
        handleLocalCommand(cmd);
    } else if (state === "connected") {
        handleRemoteCommand(cmd);
    }
}

// ── Commandes en mode LOCAL ───────────────────────────────────────
function handleLocalCommand(cmd) {
    const args = cmd.split(/\s+/);
    const command = args[0].toLowerCase();

    switch (command) {
        case "ssh":
            handleSSH(args);
            updatePrompt();
            break;
        case "help":
            appendLine("Commandes disponibles :");
            appendLine("  <span class='cmd-highlight'>ssh</span> user@host  — Se connecter à une machine distante");
            appendLine("  <span class='cmd-highlight'>ls</span>             — Lister les fichiers");
            appendLine("  <span class='cmd-highlight'>cd</span>             — Changer de dossier");
            appendLine("  <span class='cmd-highlight'>cat</span>            — Lire un fichier");
            appendLine("  <span class='cmd-highlight'>pwd</span>            — Afficher le chemin actuel");
            appendLine("  <span class='cmd-highlight'>clear</span>          — Effacer l'écran");
            appendLine("  <span class='cmd-highlight'>help</span>           — Afficher cette aide");
            updatePrompt();
            break;
        case "clear":
            output.innerHTML = "";
            updatePrompt();
            break;
        default:
            // Toutes les autres commandes (ls, cd, cat, pwd…) → serveur
            sendToServer(cmd);
            break;
    }
}

// ── Connexion SSH ─────────────────────────────────────────────────
function handleSSH(args) {
    if (args.length < 2) {
        appendLine("Usage: ssh utilisateur@adresse_ip");
        return;
    }

    const target = args[1];
    const parts  = target.split("@");

    if (parts.length !== 2 || !parts[0] || !parts[1]) {
        appendLine("ssh: format invalide. Utilisation : ssh utilisateur@adresse_ip");
        return;
    }

    sshUser = parts[0];
    sshHost = parts[1];
    attempts = 0;

    appendLine("Connexion à " + escapeHtml(sshHost) + "...");
    appendLine(escapeHtml(sshUser) + "@" + escapeHtml(sshHost) + "'s password:");

    state = "password";
    updatePrompt();
}

// ── Vérification du mot de passe ──────────────────────────────────
async function handlePasswordSubmit(password) {
    // On n'affiche PAS le mot de passe (comme un vrai SSH)
    appendLine(promptText() + "••••••••");

    try {
        const url = "/bash/exec?action=ssh_login&user=" + encodeURIComponent(sshUser) +
                    "&host=" + encodeURIComponent(sshHost) +
                    "&pass=" + encodeURIComponent(password);
        const response = await fetch(url);
        const result   = await response.json();

        if (result.authenticated) {
            // Connexion réussie
            state = "connected";
            path  = "/home";
            appendLine("");
            appendLine('<span class="ssh-banner">╔══════════════════════════════════════════════════════╗</span>');
            appendLine('<span class="ssh-banner">║     Connexion SSH établie avec succès                ║</span>');
            appendLine('<span class="ssh-banner">║     Bienvenue sur le serveur de ' + padRight(escapeHtml(sshUser), 10) + '           ║</span>');
            appendLine('<span class="ssh-banner">║                                                      ║</span>');
            appendLine('<span class="ssh-banner">║     Dernière connexion: ' + padRight(getLastLogin(), 20) + '     ║</span>');
            appendLine('<span class="ssh-banner">╚══════════════════════════════════════════════════════╝</span>');
            appendLine("");
            appendLine("Tapez <span class='cmd-highlight'>help</span> pour la liste des commandes.");
            appendLine("");
        } else {
            attempts++;
            if (attempts >= MAX_ATTEMPTS) {
                appendLine('<span class="error">Permission denied (trop de tentatives).</span>');
                appendLine('<span class="error">Connexion fermée par ' + escapeHtml(sshHost) + '.</span>');
                state   = "local";
                sshUser = "";
                sshHost = "";
            } else {
                appendLine('<span class="error">Permission denied, please try again.</span>');
                appendLine(escapeHtml(sshUser) + "@" + escapeHtml(sshHost) + "'s password:");
            }
        }
    } catch (err) {
        appendLine('<span class="error">ssh: connexion refusée par ' + escapeHtml(sshHost) + '</span>');
        state = "local";
    }

    updatePrompt();
    input.focus();
}

// ── Envoi d'une commande au serveur ───────────────────────────────
async function sendToServer(cmd) {
    const mode = (state === "connected") ? "ssh" : "local";
    const url = "/bash/exec?input=" + encodeURIComponent(cmd) + "&path=" + encodeURIComponent(path) + "&mode=" + mode;

    try {
        const response = await fetch(url);
        const result   = await response.json();

        path = result.path;

        if (result.output === "@CLEAR") {
            output.innerHTML = "";
        } else if (result.output.length > 0) {
            appendLine(result.output);
        }
    } catch (error) {
        appendLine('<span class="error">Erreur: ' + error.message + '</span>');
    }

    updatePrompt();
}

// ── Commandes en mode CONNECTÉ (remote) ───────────────────────────
function handleRemoteCommand(cmd) {
    const args    = cmd.split(/\s+/);
    const command = args[0].toLowerCase();

    // Commande "exit" / "logout" → déconnexion
    if (command === "exit" || command === "logout") {
        appendLine("Connection to " + escapeHtml(sshHost) + " closed.");
        state   = "local";
        path    = "/home";
        sshUser = "";
        sshHost = "";
        updatePrompt();
        return;
    }

    if (command === "clear") {
        output.innerHTML = "";
        updatePrompt();
        return;
    }

    // Toutes les autres commandes → serveur
    sendToServer(cmd);
}

// ── Utilitaires ───────────────────────────────────────────────────
function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

function padRight(str, len) {
    while (str.length < len) str += " ";
    return str;
}

function getLastLogin() {
    const d = new Date();
    d.setDate(d.getDate() - Math.floor(Math.random() * 5 + 1));
    return d.toLocaleDateString("fr-FR") + " " +
           d.getHours().toString().padStart(2, "0") + ":" +
           d.getMinutes().toString().padStart(2, "0");
}
