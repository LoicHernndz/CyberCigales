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

        console.log(result);

        document.getElementById("output").innerHTML += "<br>" + result.output;
        console.log(this.responseText);
        path = result.path;
    } catch (error) {
        console.error(error.message);
    }

}