let titleinput = document.getElementById("title");
titleinput.addEventListener("blur", validateEmpty);

let clientinput = document.getElementById("client");
clientinput.addEventListener("blur", validateEmpty);

let assignedinput = document.getElementById("assignedUser");
assignedinput.addEventListener("blur", validateEmpty);

let statusInput = document.getElementById("status");
statusInput.addEventListener("blur", validateEmpty);

let impactinput = document.getElementById("impact");
impactinput.addEventListener("blur", validateEmpty);

let urgencyimpact = document.getElementById("urgency");
urgencyimpact.addEventListener("blur",validateEmpty);

let descriptionInput = document.getElementById("description");
descriptionInput.addEventListener("blur", validateEmpty);

let notesInput = document.getElementById("notes");
notesInput.addEventListener("blur", validateEmpty);