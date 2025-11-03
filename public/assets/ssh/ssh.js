const input = document.getElementById("input");
input.addEventListener("keyup", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        generateOutput(input.value);
        input.value = "";
    }
});

function generateOutput(input) {
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            document.getElementById("output").innerHTML += "<br>" + this.responseText;
            console.log(this.responseText);
        }
    };
    xmlhttp.open("GET", "/ssh/exec?input=" + input, true);
    xmlhttp.send();
}