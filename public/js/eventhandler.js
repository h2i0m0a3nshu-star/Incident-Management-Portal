function validateEmail(event) {
    let input = event.target;
    let emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
    if (emailRegex.test(input.value)) {
        console.log("Valid Email");
        input.classList.remove("invalid-input");
    } else {
        input.classList.add("invalid-input");
        console.log("Invalid Email");
        event.preventDefault();
    }
}

function validatePassword(event) {
    let input = event.target;
    let passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#?!@$%^&*-]).{8,}$/;
    if (passwordRegex.test(input.value)) {
        console.log("Valid Password");
        input.classList.remove("invalid-input");
    } else {
        input.classList.add("invalid-input");
        console.log("Invalid Password");
        event.preventDefault();
    }
}

function validateEmpty(event){
    let input = event.target;
    let emptyRegex = /^$/;
    if(!emptyRegex.test(input.value)){
        input.classList.remove("invalid-input");
        console.log("Valid Password");
    }else{
        input.classList.add("invalid-input");
        console.log("InmValid Password");
        event.preventDefault();
    }
}