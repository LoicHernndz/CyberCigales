<?php
namespace Models\InterfaceWeb;

/**
 * Class InterfaceWebModel
 *
 * Modèle gérant la logique métier du navigateur web simulé (Safari).
 * Cette classe agit comme un mini-serveur web interne : elle reçoit une URL demandée,
 * vérifie si elle existe dans sa liste de pages connues, et renvoie le contenu HTML approprié
 * ou une page d'erreur 404 simulée.
 *
 * @package Models\InterfaceWeb
 */
class InterfaceWebModel
{
    /**
     * L'URL actuellement affichée dans la barre d'adresse du navigateur.
     * @var string
     */
    private string $currentUrl;

    /**
     * Le contenu HTML brut à afficher dans le corps (viewport) du navigateur.
     * @var string
     */
    private string $displayContent;

    /**
     * Tableau associatif simulant une base de données de sites web accessibles (DNS).
     *
     * Clé : Le nom de domaine (ex: 'apple.com').
     * Valeur : Le code HTML complet de la page à afficher.
     *
     * @var array<string, string>
     */
    private array $availablePages = [
        'cybercigales.fr' => '
            <div style="text-align: center;">
                <h1 style="font-size: 3rem;">CyberCigales</h1>
                <p style="font-size: 1.5rem;">Découvrez la cryptographie de manière ludique avec CyberCigales !</p>
                <div style="margin:20px auto; width:80%; height:300px; background:#f5f5f7; border-radius:10px;"></div>
            </div>',

        'google.com' => '
            <div style="text-align: center; margin-top: 100px;">
                <h1 style="color: #4285F4; font-size: 5rem; margin:0;">Google</h1>
                <input type="text" placeholder="Rechercher..." style="margin-top:20px; padding: 10px; width: 400px; border-radius: 20px; border: 1px solid #dfe1e5;">
                <div style="margin-top: 20px;">
                    <button style="padding: 10px 20px; border: 1px solid #f8f9fa; background: #f8f9fa; border-radius: 4px; cursor: pointer;">Recherche Google</button>
                    <!-- INDICE INSPECTER : TODO: Désactiver l\'accès temporaire vers dev.cybercigales.fr -->
                </div>
            </div>',

        'dev.cybercigales.fr' => '
            <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; font-family: sans-serif; background: white;">
                <h2 style="text-align: center; color: #d32f2f;">ARTICLE COMPLET</h2>
                <p style="font-size: 14px; color: #666; text-align: center;">Accès réservé au personnel de maintenance.</p>
                <div style="margin-top: 20px;">
                    <label style="display: block; margin-bottom: 5px;">Utilisateur</label>
                    <input type="text" value="admin" disabled style="width: 100%; padding: 8px; box-sizing: border-box; background: #eee;">
                </div>
                <div style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Code d\'accès</label>
                    <input type="password" value="********" disabled style="width: 100%; padding: 8px; box-sizing: border-box; background: #eee;">
                </div>
                <div style="margin-top: 25px;">
                    <button id="admin-unlock-btn" disabled style="width: 100%; padding: 12px; background: #2196f3; color: white; border: none; border-radius: 4px; cursor: not-allowed; opacity: 0.6; font-weight: bold;">
                        DÉVERROUILLER L\'ARTICLE COMPLET
                    </button>
                    <p id="unlock-message" style="display:none; color: green; font-weight: bold; margin-top: 10px; text-align: center;">Accès déverrouillé ! Regardez votre console (F12) pour accéder à l\'article complet.</p>
                </div>
                <script>
                    (function() {
                        const btn = document.getElementById("admin-unlock-btn");
                        const msg = document.getElementById("unlock-message");
                        
                        btn.addEventListener("click", function() {
                            if (!btn.hasAttribute("disabled")) {
                                msg.style.display = "block";
                                console.log("%c [ADMIN] Accès accordé ! ", "background: #222; color: #bada55; font-size: 20px;");
                                console.log("Le lien vers l\'article complet est : www.lemonde.fr/acces-complet");
                                alert("Système déverrouillé. Le code a été envoyé dans les logs administrateur (Console).");
                            } else {
                                alert("Bouton désactivé. Seul un administrateur peut modifier les attributs de la page.");
                            }
                        });
                    })();
                </script>
            </div>',


        'lemonde.fr' => '
            <style>
        /* Simulation de la feuille de style du Monde (simplifiée) */
        body {
            margin: 0;
            padding: 0;
            font-family: "Le Monde Journal", "Spectral", serif;
            color: #161616;
            background-color: #f6f6f6;
            line-height: 1.5;
        }
        .lm-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            min-height: 100vh;
        }
        
        /* Header simplifié */
        .lm-header {
            border-bottom: 1px solid #e5e5e5;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .lm-logo {
            font-family: "Le Monde Livre", serif;
            font-size: 32px;
            font-weight: bold;
        }
        .lm-nav-links a {
            text-decoration: none;
            color: #161616;
            margin-left: 15px;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
        }
        .lm-subscribe-btn {
            background-color: #ffe700;
            color: #161616;
            padding: 8px 16px;
            text-decoration: none;
            font-weight: bold;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 14px;
        }

        /* Structure Article */
        .article-header {
            max-width: 800px;
            margin: 30px auto 20px auto;
            padding: 0 20px;
        }
        .breadcrumb {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #007fff;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: bold;
        }
        h1.article-title {
            font-size: 40px;
            line-height: 1.1;
            margin: 0 0 15px 0;
            font-weight: bold;
        }
        .article-chapeau {
            font-size: 18px;
            line-height: 1.4;
            color: #161616;
            margin-bottom: 20px;
        }
        .article-meta {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #666;
            border-top: 1px solid #e5e5e5;
            padding-top: 15px;
            margin-bottom: 30px;
        }
        .article-body {
            max-width: 680px;
            margin: 0 auto;
            padding: 0 20px 50px 20px;
            font-size: 17px;
            line-height: 1.6;
        }
        .article-body p {
            margin-bottom: 20px;
        }

        /* Encarts Publicitaires (Indices) */
        .advertisement {
            margin: 35px 0;
            padding: 15px 20px;
            background-color: #f9f9f9;
            border: 1px solid #eaeaea;
            border-left: 4px solid #007fff;
            font-family: Helvetica, Arial, sans-serif;
            position: relative;
        }
        .ad-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 10px;
            display: block;
            letter-spacing: 1px;
        }
        .ad-title {
            font-weight: bold;
            font-size: 15px;
            color: #161616;
            margin-bottom: 5px;
        }
        .ad-text {
            font-size: 13px;
            color: #555;
            line-height: 1.4;
        }
        .ad-link {
            display: inline-block;
            margin-top: 8px;
            font-size: 12px;
            color: #007fff;
            text-decoration: none;
            font-weight: bold;
        }

        /* Effet de fondu (Paywall / Lock) */
        .article-fade {
            position: relative;
            max-height: 100px;
            overflow: hidden;
            margin-bottom: 40px;
        }
        .article-fade::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 80px;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1));
        }

        /* Bloc d\'accès restreint intégré */
        .admin-lock-box {
            max-width: 450px;
            margin: 0 auto;
            padding: 30px;
            border: 1px solid #e5e5e5;
            border-top: 4px solid #d32f2f;
            background: #fafafa;
            font-family: Helvetica, Arial, sans-serif;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
.admin-lock-box h2 {
    text-align: center;
            color: #d32f2f;
            margin: 0 0 5px 0;
            font-family: "Le Monde Livre", "Spectral", serif;
            font-size: 24px;
        }
        .admin-lock-box .subtitle {
    font-size: 13px;
            color: #666;
            text-align: center;
            margin-bottom: 25px;
        }
        .admin-lock-box label {
    display: block;
    margin-bottom: 6px;
            font-size: 13px;
            font-weight: bold;
            color: #333;
        }
        .admin-lock-box input {
    width: 100%;
    padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            background: #e9ecef;
            border: 1px solid #ccc;
            border-radius: 3px;
            color: #555;
            cursor: not-allowed;
        }
        .admin-lock-box button {
    width: 100%;
    padding: 14px;
            background: #161616;
            color: white;
            border: none;
            cursor: not-allowed;
            opacity: 0.8;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 0.5px;
            transition: background 0.3s;
        }
        .admin-lock-box button:not([disabled]):hover {
    background: #d32f2f;
    opacity: 1;
    cursor: pointer;
}
    </style>
</head>
<body>

    <div class="lm-container">
        <header class="lm-header">
            <div class="lm-logo">Le Monde</div>
            <div class="lm-nav-links">
                <a href="#">Actualités</a>
                <a href="#">Économie</a>
                <a href="#">Pixels</a>
                <a href="#" class="lm-subscribe-btn">S’abonner</a>
            </div>
        </header>

        <article>
            <header class="article-header">
                <div class="breadcrumb">Pixels > Cybersécurité</div>
                <h1 class="article-title">Le terminal Linux : de la navigation élémentaire à la compréhension de l\'attaque par force brute</h1>
                <p class="article-chapeau">
                    Comprendre les commandes fondamentales pour se repérer dans un système d\'exploitation est la première étape vers la maîtrise de l\'informatique. C\'est aussi le prérequis nécessaire pour appréhender des méthodes d\'attaque rudimentaires mais efficaces, comme le "brute force".
                </p>
                <div class="article-meta">
                    Par la rédaction Pixels (avec CyberCigales) <br>
                    <time datetime="2026-02-11">Publié le 11 février 2026 à 11h42</time>
                </div>
            </header>

            <div class="article-body">
                <p>
                    Face à un écran noir où seul clignote un curseur blanc, beaucoup d\'utilisateurs ressentent une appréhension. Le terminal, cette interface textuelle omniprésente sur les systèmes Linux (qui propulsent la majorité des serveurs web mondiaux), semble austère. Pourtant, il s\'agit de l\'outil le plus direct pour dialoguer avec la machine.
                </p>

                <div class="advertisement">
                    <span class="ad-label">Sponsorisé par l\'Institut des Outils Dev</span>
                    <div class="ad-title">Formation Web : Apprenez à regarder sous la surface</div>
                    <div class="ad-text">Face à un élément web qui vous résiste, la solution est souvent à portée de <strong>clic droit</strong>. Apprenez à <strong>inspecter</strong> le code de vos pages pour comprendre ce qui se cache vraiment derrière l\'interface utilisateur.</div>
                    <a href="#" class="ad-link">Découvrir le programme ></a>
                </div>
                
                <div class="article-fade">
                    <p>
Loin d\'être réservé aux experts, l\'usage du terminal repose sur quelques concepts clés analogues à la navigation dans un bâtiment. Avant de construire des forteresses numériques, il faut savoir ouvrir les portes et lire les panneaux...
                    </p>
                </div>

                <div class="admin-lock-box">
                    <h2>ARTICLE COMPLET</h2>
                    <p class="subtitle">Accès restreint : réservé au personnel de maintenance.</p>
                    
                    <div>
                        <label>Utilisateur</label>
                        <input type="text" value="admin" disabled>
                    </div>
                    
                    <div>
                        <label>Code d\'accès</label>
                        <input type="password" value="********" disabled>
                    </div>
                    
                    <div>
                        <button id="admin-unlock-btn" disabled>DÉVERROUILLER L\'ARTICLE</button>
                        <p id="unlock-message" style="display:none; color: #2e7d32; font-weight: bold; margin-top: 15px; text-align: center; font-size: 13px;">
    Accès accordé. Vérifiez la console administrateur (F12).
                        </p>
                    </div>
                </div>

                <div class="advertisement" style="margin-top: 40px; border-left-color: #ff9800;">
                    <span class="ad-label">Publicité - Mindset Pro</span>
                    <div class="ad-title">Coaching de vie : Ne restez plus bloqué</div>
                    <div class="ad-text">Vous vous sentez freiné ou <em>désactivé</em> (<strong>disabled</strong>) face aux défis du quotidien ? Notre méthode vous aide à <strong>supprimer les attributs</strong> négatifs de votre vie pour reprendre immédiatement le contrôle de vos actions.</div>
                    <a href="#" class="ad-link" style="color: #ff9800;">Commencer ma thérapie ></a>
                </div>

                <script>
(function() {
    const btn = document.getElementById("admin-unlock-btn");
    const msg = document.getElementById("unlock-message");

    btn.addEventListener("click", function() {
        if (!btn.hasAttribute("disabled")) {
            msg.style.display = "block";
            console.log("%c [ADMIN] Accès accordé ! ", "background: #222; color: #bada55; font-size: 20px;");
            console.log("Le lien vers l\'article complet est : www.lemonde.fr-acces-complet");
            alert("Système déverrouillé. Le code a été envoyé dans les logs administrateur (Console).");
        } else {
            alert("Bouton désactivé. Seul un administrateur peut modifier les attributs de la page.");
        }
    });
})();
                </script>
            </div>
        </article>
    </div>',

        'haveibeenpwned.music' => '
            <style>
                .hibp-container { max-width: 800px; margin: 0 auto; font-family: "Segoe UI", Arial, sans-serif; }
                .hibp-header { background: #2d2d2d; color: white; padding: 20px 30px; text-align: center; }
                .hibp-header h1 { font-size: 28px; margin: 0 0 5px 0; }
                .hibp-header h1 span { color: #f44336; }
                .hibp-header p { color: #aaa; font-size: 14px; margin: 0; }
                .hibp-search { text-align: center; padding: 30px; background: #f5f5f5; }
                .hibp-search input { width: 350px; padding: 12px 16px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px; }
                .hibp-search button { padding: 12px 24px; background: #1976d2; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; margin-left: 10px; }
                .hibp-search button:hover { background: #1565c0; }
                .hibp-result { display: none; margin: 20px 30px; padding: 20px; border-radius: 8px; }
                .hibp-result.danger { background: #ffebee; border: 2px solid #f44336; }
                .hibp-result.danger h2 { color: #d32f2f; margin-top: 0; }
                .hibp-breach { background: white; border: 1px solid #eee; padding: 15px; margin: 10px 0; border-radius: 6px; }
                .hibp-breach h3 { margin: 0 0 8px 0; color: #d32f2f; }
                .hibp-breach p { margin: 4px 0; font-size: 14px; color: #555; }
                .hibp-info { margin: 30px; padding: 20px; background: #e3f2fd; border-radius: 8px; border-left: 4px solid #1976d2; }
                .hibp-info h3 { margin-top: 0; color: #1565c0; }
                .hibp-info a { color: #1976d2; font-weight: bold; }
                .hibp-timer-bar { display:none; margin: 20px 30px; padding: 20px; background: #fff3e0; border: 2px solid #ff9800; border-radius: 8px; text-align: center; }
                .hibp-timer-bar h2 { color: #e65100; margin-top: 0; }
                .hibp-timer-display { font-size: 48px; font-weight: bold; font-family: monospace; color: #d32f2f; }
                .hibp-pwd-form { margin: 15px 0; }
                .hibp-pwd-form label { display: block; font-weight: bold; margin: 10px 0 4px; text-align: left; font-size: 14px; }
                .hibp-pwd-form input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
                .hibp-pwd-form .pwd-row { background: #fff; border: 1px solid #eee; padding: 12px; margin: 8px 0; border-radius: 6px; text-align: left; }
                .hibp-pwd-form .pwd-site { font-weight: bold; color: #333; }
                .hibp-pwd-form .pwd-old { font-size: 12px; color: #999; }
                .hibp-pwd-form .pwd-status { float: right; font-size: 12px; padding: 2px 8px; border-radius: 10px; }
                .pwd-status.done { background: #c8e6c9; color: #2e7d32; }
                .pwd-status.pending { background: #ffcdd2; color: #c62828; }
                .hibp-fail-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; color:#f44336; font-family:monospace; }
                .hibp-fail-content { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; max-width:500px; }
                .hibp-success { display:none; margin: 20px 30px; padding: 20px; background: #e8f5e9; border: 2px solid #4caf50; border-radius: 8px; text-align: center; }
                .hibp-success h2 { color: #2e7d32; margin-top: 0; }
                .hibp-hacker-result { display:none; margin: 20px 30px; padding: 20px; background: #fce4ec; border: 2px solid #e91e63; border-radius: 8px; }
                .hibp-hacker-result h2 { color: #880e4f; margin-top: 0; }
                .hibp-db-table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
                .hibp-db-table th { background: #333; color: white; padding: 8px 12px; text-align: left; }
                .hibp-db-table td { padding: 8px 12px; border-bottom: 1px solid #eee; }
                .hibp-db-table tr:hover { background: #fff9c4; }
                .hibp-db-table .highlight { background: #ffeb3b; font-weight: bold; }
            </style>
            <div class="hibp-container">
                <div class="hibp-header">
                    <h1>have i been <span>pwned?</span></h1>
                    <p>Vérifiez si votre adresse email a été compromise dans une fuite de données</p>
                </div>
                <div class="hibp-search">
                    <input type="text" id="hibp-email" placeholder="Entrez une adresse email...">
                    <button id="hibp-check-btn">Vérifier</button>
                </div>

                <!-- Résultat : email utilisateur compromis -->
                <div class="hibp-result" id="hibp-result-danger">
                    <h2>⚠️ Oh non — cet email a été compromis !</h2>
                    <p>Cette adresse a été trouvée dans <strong>3 fuites de données</strong> connues :</p>
                    <div class="hibp-breach">
                        <h3>SocialApp Data Breach (2024)</h3>
                        <p><strong>Données exposées :</strong> Email, mot de passe hashé, date de naissance</p>
                    </div>
                    <div class="hibp-breach">
                        <h3>GamePlatform Leak (2023)</h3>
                        <p><strong>Données exposées :</strong> Email, pseudo, adresse IP</p>
                    </div>
                    <div class="hibp-breach">
                        <h3>ShopOnline Hack (2023)</h3>
                        <p><strong>Données exposées :</strong> Email, nom complet, adresse postale</p>
                    </div>
                    <p style="color:#d32f2f;font-weight:bold;margin-top:15px;">⏰ Vous devez changer vos mots de passe immédiatement ! Cliquez ci-dessous pour lancer la procédure de sécurisation.</p>
                    <button id="hibp-start-timer" style="padding:12px 30px;background:#d32f2f;color:white;border:none;border-radius:6px;font-size:16px;cursor:pointer;margin-top:10px;">Sécuriser mes comptes maintenant</button>
                </div>

                <!-- Timer + changement de mots de passe -->
                <div class="hibp-timer-bar" id="hibp-timer-section">
                    <h2>🔒 Sécurisation en cours</h2>
                    <p>Changez vos mots de passe avant la fin du temps imparti !</p>
                    <div class="hibp-timer-display" id="hibp-timer">5:00</div>
                    <div class="hibp-pwd-form" id="hibp-pwd-form">
                        <div class="pwd-row" data-site="socialapp">
                            <span class="pwd-site">SocialApp</span>
                            <span class="pwd-status pending" id="status-socialapp">Non modifié</span>
                            <label>Nouveau mot de passe :</label>
                            <input type="password" id="pwd-socialapp" placeholder="Minimum 8 caractères">
                            <button class="hibp-pwd-btn" data-target="socialapp" style="margin-top:6px;padding:6px 16px;background:#1976d2;color:white;border:none;border-radius:4px;cursor:pointer;">Changer</button>
                        </div>
                        <div class="pwd-row" data-site="gameplatform">
                            <span class="pwd-site">GamePlatform</span>
                            <span class="pwd-status pending" id="status-gameplatform">Non modifié</span>
                            <label>Nouveau mot de passe :</label>
                            <input type="password" id="pwd-gameplatform" placeholder="Minimum 8 caractères">
                            <button class="hibp-pwd-btn" data-target="gameplatform" style="margin-top:6px;padding:6px 16px;background:#1976d2;color:white;border:none;border-radius:4px;cursor:pointer;">Changer</button>
                        </div>
                        <div class="pwd-row" data-site="shoponline">
                            <span class="pwd-site">ShopOnline</span>
                            <span class="pwd-status pending" id="status-shoponline">Non modifié</span>
                            <label>Nouveau mot de passe :</label>
                            <input type="password" id="pwd-shoponline" placeholder="Minimum 8 caractères">
                            <button class="hibp-pwd-btn" data-target="shoponline" style="margin-top:6px;padding:6px 16px;background:#1976d2;color:white;border:none;border-radius:4px;cursor:pointer;">Changer</button>
                        </div>
                    </div>
                </div>

                <!-- Succès : comptes sécurisés -->
                <div class="hibp-success" id="hibp-success">
                    <h2>✅ Tous vos comptes ont été sécurisés !</h2>
                    <p>Vos mots de passe ont été changés à temps. Bien joué !</p>
                    <p style="margin-top:15px;font-size:14px;color:#555;">Vous pouvez maintenant utiliser la barre de recherche ci-dessus pour chercher d\'autres adresses email...</p>
                </div>

                <!-- Résultat : email du hackeur trouvé dans une base fuitée -->
                <div class="hibp-hacker-result" id="hibp-hacker-result">
                    <h2>💀 Email trouvé dans une base de données fuitée !</h2>
                    <p>L\'adresse <strong>k0de_breaker@darkweb.net</strong> apparaît dans la fuite <strong>DarkForumDB (2024)</strong>.</p>
                    <p>Voici un extrait de la base de données compromise :</p>
                    <table class="hibp-db-table">
                        <thead>
                            <tr><th>Email</th><th>Username</th><th>Password</th><th>Last Login</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>shadow_x@mail.ru</td><td>ShadowX</td><td>*****</td><td>2024-01-15</td></tr>
                            <tr><td>null_byte@proton.me</td><td>NullByte</td><td>*****</td><td>2024-02-03</td></tr>
                            <tr class="highlight"><td>k0de_breaker@darkweb.net</td><td>K0de_Breaker</td><td>EmailS3cret!</td><td>2024-03-22</td></tr>
                            <tr><td>ghost99@tutanota.com</td><td>Gh0st99</td><td>*****</td><td>2024-01-28</td></tr>
                            <tr><td>cyph3r@riseup.net</td><td>Cyph3rPunk</td><td>*****</td><td>2024-02-14</td></tr>
                        </tbody>
                    </table>
                    <p style="margin-top:15px;color:#880e4f;font-weight:bold;">Le mot de passe du hackeur est visible ! Utilisez-le pour accéder à sa boîte mail et aider votre amie.</p>
                </div>

                <!-- Échec : temps écoulé -->
                <div class="hibp-fail-overlay" id="hibp-fail-overlay">
                    <div class="hibp-fail-content">
                        <h1 style="font-size:48px;">⏰ Temps écoulé !</h1>
                        <p style="font-size:18px;color:#ff6b6b;line-height:1.6;">Vous n\'avez pas changé vos mots de passe à temps.<br>Votre compte s\'est fait pirater !</p>
                        <p style="font-size:14px;color:#888;margin-top:20px;">Vous devez recommencer. La prochaine fois, soyez plus rapide !</p>
                        <button id="hibp-retry" style="margin-top:20px;padding:12px 30px;background:#f44336;color:white;border:none;border-radius:6px;font-size:16px;cursor:pointer;">Recommencer</button>
                    </div>
                </div>

                <div class="hibp-info">
                    <h3>🔍 Testez avec votre vrai email !</h3>
                    <p>Ce site est une simulation éducative. Pour vérifier si votre vrai email a fuité, rendez-vous sur le vrai site :</p>
                    <p><a href="https://haveibeenpwned.com" target="_blank">👉 haveibeenpwned.com</a></p>
                    <p style="font-size:13px; color:#555;">C\'est gratuit, fiable, et créé par Troy Hunt, un expert reconnu en cybersécurité.</p>
                </div>
            </div>
            <script>
            (function() {
                const HACKER_EMAIL = "k0de_breaker@darkweb.net";
                const TIMER_SECONDS = 300; // 5 minutes

                const btn = document.getElementById("hibp-check-btn");
                const input = document.getElementById("hibp-email");
                const dangerResult = document.getElementById("hibp-result-danger");
                const startTimerBtn = document.getElementById("hibp-start-timer");
                const timerSection = document.getElementById("hibp-timer-section");
                const timerDisplay = document.getElementById("hibp-timer");
                const successDiv = document.getElementById("hibp-success");
                const hackerResult = document.getElementById("hibp-hacker-result");
                const failOverlay = document.getElementById("hibp-fail-overlay");
                const retryBtn = document.getElementById("hibp-retry");

                let timerInterval = null;
                let timeLeft = TIMER_SECONDS;
                let passwordsChanged = { socialapp: false, gameplatform: false, shoponline: false };
                let challengeCompleted = localStorage.getItem("hibp_challenge_done") === "true";

                // Recherche email
                btn.addEventListener("click", function() {
                    const email = input.value.trim().toLowerCase();
                    dangerResult.style.display = "none";
                    hackerResult.style.display = "none";

                    if (!email || !email.includes("@")) {
                        alert("Veuillez entrer une adresse email valide.");
                        return;
                    }

                    if (email === HACKER_EMAIL) {
                        hackerResult.style.display = "block";
                        return;
                    }

                    if (!challengeCompleted) {
                        dangerResult.style.display = "block";
                    } else {
                        dangerResult.style.display = "block";
                        startTimerBtn.style.display = "none";
                        dangerResult.querySelector("p[style]").textContent = "Vos comptes ont déjà été sécurisés.";
                    }
                });

                input.addEventListener("keydown", function(e) {
                    if (e.key === "Enter") btn.click();
                });

                // Lancer le timer
                startTimerBtn.addEventListener("click", function() {
                    dangerResult.style.display = "none";
                    timerSection.style.display = "block";
                    timeLeft = TIMER_SECONDS;
                    passwordsChanged = { socialapp: false, gameplatform: false, shoponline: false };
                    updateTimer();
                    timerInterval = setInterval(function() {
                        timeLeft--;
                        updateTimer();
                        if (timeLeft <= 0) {
                            clearInterval(timerInterval);
                            failOverlay.style.display = "block";
                        }
                    }, 1000);
                });

                function updateTimer() {
                    const min = Math.floor(timeLeft / 60);
                    const sec = timeLeft % 60;
                    timerDisplay.textContent = min + ":" + (sec < 10 ? "0" : "") + sec;
                    if (timeLeft <= 60) timerDisplay.style.color = "#d32f2f";
                    else if (timeLeft <= 120) timerDisplay.style.color = "#ff9800";
                    else timerDisplay.style.color = "#2e7d32";
                }

                // Boutons changer mot de passe
                document.querySelectorAll(".hibp-pwd-btn").forEach(function(b) {
                    b.addEventListener("click", function() {
                        var site = b.getAttribute("data-target");
                        var pwd = document.getElementById("pwd-" + site).value;
                        if (pwd.length < 8) {
                            alert("Le mot de passe doit contenir au moins 8 caractères !");
                            return;
                        }
                        passwordsChanged[site] = true;
                        document.getElementById("status-" + site).textContent = "Modifié ✓";
                        document.getElementById("status-" + site).className = "pwd-status done";
                        b.disabled = true;
                        b.textContent = "Fait !";
                        b.style.background = "#4caf50";

                        if (passwordsChanged.socialapp && passwordsChanged.gameplatform && passwordsChanged.shoponline) {
                            clearInterval(timerInterval);
                            timerSection.style.display = "none";
                            successDiv.style.display = "block";
                            challengeCompleted = true;
                            localStorage.setItem("hibp_challenge_done", "true");
                        }
                    });
                });

                // Recommencer
                retryBtn.addEventListener("click", function() {
                    failOverlay.style.display = "none";
                    timerSection.style.display = "none";
                    dangerResult.style.display = "block";
                });
            })();
            </script>',

        'lemonde.fr-acces-complet' => '

            <style>
        /* Simulation de la feuille de style du Monde (simplifiée pour cet exemple) */
        body {
            margin: 0;
            padding: 0;
            font-family: "Le Monde Journal", "Spectral", serif;
            color: #161616;
            background-color: #f6f6f6;
            line-height: 1.5;
        }
        .lm-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        
        /* Header simplifié */
        .lm-header {
            border-bottom: 1px solid #e5e5e5;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .lm-logo {
            font-family: "Le Monde Livre", serif;
            font-size: 32px;
            font-weight: bold;
        }
        .lm-nav-links a {
            text-decoration: none;
            color: #161616;
            margin-left: 15px;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
        }
        .lm-subscribe-btn {
            background-color: #ffe700;
            color: #161616;
            padding: 8px 16px;
            text-decoration: none;
            font-weight: bold;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 14px;
        }

        /* Structure Article */
        .article-header {
            max-width: 800px;
            margin: 30px auto 20px auto;
            padding: 0 20px;
        }
        .breadcrumb {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #007fff;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: bold;
        }
        h1.article-title {
            font-size: 40px;
            line-height: 1.1;
            margin: 0 0 15px 0;
            font-weight: bold;
        }
        .article-chapeau {
            font-size: 18px;
            line-height: 1.4;
            color: #161616;
            margin-bottom: 20px;
        }
        .article-meta {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #666;
            border-top: 1px solid #e5e5e5;
            padding-top: 15px;
            margin-bottom: 30px;
        }
        .article-body {
            max-width: 680px;
            margin: 0 auto;
            padding: 0 20px 50px 20px;
            font-size: 17px;
            line-height: 1.6;
        }
        .article-body p {
            margin-bottom: 20px;
        }
        .article-body h2 {
            font-size: 24px;
            margin-top: 35px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        code.terminal-cmd {
            font-family: "Courier New", Courier, monospace;
            background-color: #f0f0f0;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 0.9em;
        }
        /* Images dans l\'article */
        figure.article-image {
            margin: 30px -40px;
        }
        figure.article-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        figcaption {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 13px;
            color: #666;
            margin-top: 10px;
            padding-left: 40px;
        }
    </style>
            <div class="lm-container">
    <header class="lm-header">
        <div class="lm-logo">Le Monde</div>
        <div class="lm-nav-links">
            <a href="#">Actualités</a>
            <a href="#">Économie</a>
            <a href="#">Pixels</a>
            <a href="#" class="lm-subscribe-btn">S’abonner</a>
        </div>
    </header>

    <article>
        <header class="article-header">
            <div class="breadcrumb">Pixels > Cybersécurité</div>
            <h1 class="article-title">Le terminal Linux : de la navigation élémentaire à la compréhension de l\'attaque par force brute</h1>
            <p class="article-chapeau">
                Comprendre les commandes fondamentales pour se repérer dans un système d\'exploitation est la première étape vers la maîtrise de l\'informatique. C\'est aussi le prérequis nécessaire pour appréhender des méthodes d\'attaque rudimentaires mais efficaces, comme le "brute force".
            </p>
            <div class="article-meta">
                Par la rédaction Pixels (avec CyberCigales) <br>
                <time datetime="2026-02-11">Publié le 11 février 2026 à 11h42</time>
            </div>
        </header>

        <div class="article-body">
            <p>
                Face à un écran noir où seul clignote un curseur blanc, beaucoup d\'utilisateurs ressentent une appréhension. Le terminal, cette interface textuelle omniprésente sur les systèmes Linux (qui propulsent la majorité des serveurs web mondiaux), semble austère. Pourtant, il s\'agit de l\'outil le plus direct pour dialoguer avec la machine.
            </p>
            <p>
                Loin d\'être réservé aux experts, l\'usage du terminal repose sur quelques concepts clés analogues à la navigation dans un bâtiment. Avant de construire des forteresses numériques, il faut savoir ouvrir les portes et lire les panneaux.
            </p>

            <h2>Les fondations de la navigation</h2>

            <p>
                La première question que l\'on se pose dans un environnement inconnu est : « Où suis-je ? ». Dans le terminal, la réponse est apportée par la commande <code class="terminal-cmd">pwd</code> (Print Working Directory). Elle affiche le chemin complet du dossier dans lequel l\'utilisateur se trouve actuellement. C\'est votre point GPS.
            </p>

            <figure class="article-image">
                <div style="background:#000; color:#0f0; padding:20px; font-family:monospace; text-align:left;">
                    user@linux:~$ pwd<br>
                    /home/user<br>
                    user@linux:~$ ls<br>
                    Documents  Images  Musique  config.txt<br>
                    user@linux:~$ _
                </div>
                <figcaption>Capture d\'écran d\'un terminal montrant l\'utilisation des commandes de base pour se repérer. (Capture d\'écran)</figcaption>
            </figure>

            <p>
                Une fois localisé, il faut observer son environnement. La commande <code class="terminal-cmd">ls</code> (List) est l\'équivalent d\'allumer la lumière dans une pièce : elle liste tous les fichiers et dossiers présents autour de vous. C\'est l\'outil d\'inventaire par excellence.
            </p>
            <p>
                Pour se déplacer, l\'utilisateur emploie <code class="terminal-cmd">cd</code> (Change Directory). Taper <code class="terminal-cmd">cd Documents</code> revient à ouvrir la porte du bureau "Documents" et à y entrer. C’est la commande fondamentale du mouvement dans l’arborescence du système.
            </p>

            <h2>Lire le système pour mieux le comprendre</h2>

            <p>
                Se déplacer est utile, mais interagir est nécessaire. Pour lire le contenu d\'un fichier sans l\'ouvrir dans un éditeur complexe, la commande <code class="terminal-cmd">cat</code> est l\'outil de prédilection.
            </p>
            <p>
                Si l\'on tape <code class="terminal-cmd">cat config.txt</code>, le terminal va littéralement « cracher » le contenu textuel de ce fichier directement à l\'écran. C\'est une commande cruciale, souvent utilisée par les administrateurs pour vérifier rapidement des journaux d\'événements (logs) ou des fichiers de configuration.
            </p>

            <h2>De l\'outil à l\'arme : l\'attaque par force brute</h2>

            <p>
                La maîtrise de ces commandes de base (<code class="terminal-cmd">cd</code> pour aller au bon endroit, <code class="terminal-cmd">ls</code> pour trouver la cible, <code class="terminal-cmd">cat</code> pour tenter de lire des informations) est le socle de l\'administration système. Paradoxalement, c\'est aussi le socle de la pensée d\'un attaquant.
            </p>
            <p>
                Lorsqu\'un système est verrouillé par un mot de passe, une des méthodes d\'attaque les plus anciennes, et conceptuellement la plus simple, est la « force brute » (brute force).
            </p>

            <figure class="article-image">
                 <div style="background:#ddd; height:250px; display:flex; justify-content:center; align-items:center; color:#666; font-style:italic;">
                    [Illustration : Un cadenas numérique bombardé de milliers de clés différentes]
                </div>
                <figcaption>L\'attaque par force brute consiste à tester épuisement toutes les combinaisons possibles jusqu\'à trouver la bonne. (Crédits : Getty Images)</figcaption>
            </figure>

            <p>
                Imaginez un cambrioleur face à un digicode à quatre chiffres. Au lieu de chercher une faille complexe dans le mécanisme, il décide de taper 0000, puis 0001, puis 0002, et ainsi de suite jusqu\'à 9999. Il est mathématiquement certain de finir par entrer.
            </p>
            <p>
                En informatique, c\'est exactement la même chose. Un attaquant n\'utilise pas ses mains, mais des scripts automatisés qui s\'interfacent avec le terminal. Ces robots vont tenter des millions de combinaisons de mots de passe par seconde contre un formulaire de connexion ou un accès serveur.
            </p>
            <p>
                Si le mot de passe est faible (comme "123456" ou "password"), l\'attaque par force brute réussira en quelques secondes. L\'attaquant obtiendra alors un accès légitime au terminal, et pourra utiliser les commandes <code class="terminal-cmd">cd</code>, <code class="terminal-cmd">ls</code> et <code class="terminal-cmd">cat</code> pour explorer et exfiltrer des données sensibles. Comprendre comment se protéger commence par comprendre la simplicité déconcertante de cette menace.
            </p>
        </div>
    </article>
    </div>
                <script>
                    (function() {
                        // Vérifier si la notification a déjà été déclenchée (éviter les doublons)
                        if (localStorage.getItem("lemonde_notif_triggered") === "true") {
                            console.log("Notification Instagram déjà déclenchée précédemment.");
                            return;
                        }

                        console.log("Article complet ouvert ! Préparation de la notification Instagram...");

                        // Marquer immédiatement comme déclenché (persiste même si l\'iframe ferme)
                        localStorage.setItem("lemonde_notif_triggered", "true");

                        const notificationData = {
                            username: "mel_133",
                            avatar: "/images/instagram/faux-profil-amie-hacke/melina_photo_selfie_salon.png",
                            caption: "Nouvelle publication vidéo : Découvrez ma plateforme crypto !",
                            timestamp: Date.now()
                        };

                        // 1. Appeler l\'API PHP pour débloquer le post immédiatement
                        fetch("/instagram/unlock-post")
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    console.log("Post Instagram débloqué côté serveur.");
                                }
                            })
                            .catch(err => console.error("Erreur unlock-post:", err));

                        // 2. Programmer la notification sur le PARENT (le bureau macOS) 
                        //    Le timer vit sur le parent, donc il persiste même si le journal est fermé
                        localStorage.setItem("instagram_notifications", "[]");
                        localStorage.setItem("instagram_unread_count", "0");

                        if (window.parent && window.parent !== window && typeof window.parent.scheduleNotification === "function") {
                            // Planifier la notification sur le parent proprement via helper
                            window.parent.scheduleNotification(notificationData, 30000);
                        } else if (window.parent && window.parent !== window && typeof window.parent.showNotification === "function") {
                            // Fallback ancien système
                            window.parent.setTimeout(function() {
                                window.parent.showNotification(notificationData);
                            }, 30000);
                        } else {
                            // Fallback LS
                            setTimeout(function() {
                                localStorage.setItem("instagram_notifications", JSON.stringify([notificationData]));
                                localStorage.setItem("instagram_unread_count", "1");
                                window.dispatchEvent(new Event("storage"));
                            }, 30000);
                        }
                    })();
                </script>
            </div>'
    ];

    /**
     * Constructeur.
     * Initialise le navigateur sur la page d'accueil par défaut (Apple).
     */
    public function __construct()
    {
        $this->currentUrl = "google.com";
        $this->displayContent = $this->availablePages['google.com'];
    }

    /**
     * Traite la requête utilisateur (changement d'URL).
     *
     * Cette méthode vérifie si un formulaire POST a été soumis. Si oui :
     * 1. Elle nettoie l'entrée utilisateur (suppression de http://, www, etc.).
     * 2. Elle cherche si la page existe dans $availablePages.
     * 3. Elle met à jour $displayContent avec la page trouvée ou une erreur 404 générée.
     *
     * @return void
     */
    public function handleRequest(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['url_input'])) {

            // 1. On récupère ce que l'utilisateur a tapé
            $rawInput = trim($_POST['url_input']);

            // 2. On nettoie l'URL pour trouver la "clé"
            // strtolower() EN PREMIER sur $rawInput, puis on supprime les préfixes (www. etc.)
            // Ordre important : si on fait strtolower après, 'WWW.' n'est pas supprimé par str_replace('www.'...)
            $cleanKey = str_replace(['https://', 'http://', 'www.', '/'], '', strtolower($rawInput));

            // On met à jour l'URL affichée
            $this->currentUrl = $rawInput;

            // 3. On vérifie si la clé existe dans notre tableau
            if (array_key_exists($cleanKey, $this->availablePages)) {
                // La page existe
                $this->displayContent = $this->availablePages[$cleanKey];
            } else {
                // La page n'existe pas -> On génère la fausse 404
                $this->displayContent = $this->generateFake404($rawInput);
            }
        }
    }

    /**
     * Génère le code HTML d'une fausse page d'erreur "Introuvable".
     *
     * Cette méthode crée une page qui ressemble à une erreur de navigateur,
     * mais qui est techniquement une page valide affichée par l'application.
     *
     * @param string $url L'URL invalide saisie par l'utilisateur.
     * @return string Le code HTML de la page d'erreur.
     */
    private function generateFake404(string $url): string
    {
        return '
            <div style="text-align: center; color: #333; margin-top: 50px;">
                <h1 style="font-size: 40px; color: #555; margin-bottom:10px;">Introuvable</h1>
                <p style="font-size: 18px; color: #888;">Safari ne parvient pas à ouvrir la page.</p>
                
                <div style="background: #fff; border: 1px solid #ccc; padding: 20px; display: inline-block; margin-top: 30px; border-radius: 6px; max-width: 500px; text-align:left;">
                    <p style="margin:0;"><strong>Erreur :</strong> Impossible de trouver le serveur associé à l\'adresse :</p>
                    <p style="color: blue; margin-top:5px;">' . htmlspecialchars($url) . '</p>
                    <hr style="margin: 15px 0; border:0; border-top:1px solid #eee;">
                    <p style="font-size: 12px; color: #aaa;">Essayez de taper <strong>apple.com</strong> ou <strong>google.com</strong>.</p>
                </div>
            </div>
        ';
    }

    /**
     * Récupère l'URL courante.
     *
     * @return string
     */
    public function getCurrentUrl(): string
    {
        return $this->currentUrl;
    }

    /**
     * Récupère le contenu HTML à afficher.
     *
     * @return string
     */
    public function getDisplayContent(): string
    {
        return $this->displayContent;
    }
}