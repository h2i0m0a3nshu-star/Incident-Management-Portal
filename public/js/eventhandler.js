function validateEmail(event){
    let input = event.target;
    let emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
    if(emailRegex.test(input.value)){
        console.log("Valid Email");
        return true;
    }
    console.log("Invalid Email");
}

function validatePassword(event){
    let input = event.target;
    let passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#?!@$%^&*-]).{8,}$/;
    if(passwordRegex.test(input.value)){
        console.log("Valid Password");
        return true;
    }
    console.log("Invalid Password");
}