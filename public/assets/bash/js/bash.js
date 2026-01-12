const input = document.getElementById("input");
let path = "/home";
input.addEventListener("keyup", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        generateOutput(input.value);
        input.value = "";
    }
});

async function generateOutput(input) {
    let url = "/bash/exec?input=" + input + "&path=" + path
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        let result = await response.json();

        path = result.path;
        document.getElementById("output").innerHTML += input;

        if (result.output.length === 0) document.getElementById("output").innerHTML += "<br>" + path.split("/").pop() + "> ";
        else if (result.output === "@CLEAR") document.getElementById("output").innerHTML = path.split("/").pop() + "> ";   // Outputs speciaux
        else document.getElementById("output").innerHTML += "<br>" + result.output + "<br>" + path.split("/").pop() + "> ";

    } catch (error) {
        console.error(error.message);
    }
}

document.getElementById("output").innerHTML = path.split("/").pop() + "> ";