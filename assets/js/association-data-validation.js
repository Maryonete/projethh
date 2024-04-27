// Définition des champs de formulaire pour le référent
const formFieldsReferent = document.querySelectorAll(".js-referent");

// Fonction pour afficher un formulaire et masquer l'autre
const afficherFormulaire = (formulaireAAfficher, formulaireAMasquer) => {
  document.querySelector(formulaireAAfficher).style.display = "block";
  cacheFormulaire(formulaireAMasquer);
};
// Fonction pour cacher formulaire
const cacheFormulaire = (formulaireAMasquer) => {
  document.querySelector(formulaireAMasquer).style.display = "none";
};
// Fonction pour activer ou désactiver les champs d'un formulaire
const activerDesactiverChamps = (formFields, disable) => {
  formFields.forEach(function (field) {
    if (disable) {
      console.log("Rendre disabled : " + formFields);
      field.setAttribute("disabled", "disabled");
    } else {
      field.removeAttribute("disabled");
    }
  });
};
const formFieldsPresident = document.querySelectorAll(".js-president");
// Fonction pour modifier le président
const modifierPresident = (evt) => {
  evt.preventDefault();
  afficherFormulaire("#modifierPresidentForm", "#createPresidentForm");
  activerDesactiverChamps(formFieldsPresident, true);
};
// Fonction pour modifier le référent
const modifierReferent = (evt) => {
  evt.preventDefault();
  afficherFormulaire("#modifierReferent", "#createReferentForm");
  activerDesactiverChamps(formFieldsReferent, true);
};
// Fonction générique pour ajouter un nouveau président ou référent
const ajouterNouveauRole = (evt, formId, formFields, modifierFormId) => {
  evt.preventDefault();
  afficherFormulaire(formId, modifierFormId);
  activerDesactiverChamps(formFields, false);
};

// Utilisation pour l'ajout d'un nouveau président
const ajouterNouveauPresident = (evt) => {
  ajouterNouveauRole(
    evt,
    "#createPresidentForm",
    formFieldsPresident,
    "#modifierPresidentForm"
  );
};

// Utilisation pour l'ajout d'un nouveau référent
const ajouterNouveauReferent = (evt) => {
  ajouterNouveauRole(
    evt,
    "#createReferentForm",
    formFieldsReferent,
    "#modifierReferent"
  );
};

// Écouteur d'événement pour la case à cocher "Président(e) est Référent(e)"

// referent = president
let presidentIsReferentCheckbox = document.querySelector(
  "input[id=campain_association_presidentIsReferent]"
);

presidentIsReferentCheckbox.addEventListener("change", () => {
  cacheFormulaire("#createReferentForm");
  if (presidentIsReferentCheckbox.checked) {
    console.log("presidentIsReferentCheckbox");
    activerDesactiverChamps(formFieldsReferent, true);

    document
      .querySelectorAll(".js-referent_non_president")
      .forEach(function (element) {
        element.style.display = "none";
      });

    document.querySelector(
      "#campain_association_association_referent_tel"
    ).style.display = "block";
    document.querySelector("#modifierReferent").style.display = "block";
    const presidentFirstName = document.querySelector(
      "#campain_association_association_president_user_firstname"
    );
    const presidentLastName = document.querySelector(
      "#campain_association_association_president_user_lastname"
    );
    const presidentEmail = document.querySelector(
      "#campain_association_association_president_user_email"
    );

    const referentFirstNameInput = document.querySelector(
      "#campain_association_association_referent_user_firstname"
    );
    const referentLastNameInput = document.querySelector(
      "#campain_association_association_referent_user_lastname"
    );
    const referentEmailInput = document.querySelector(
      "#campain_association_association_referent_user_email"
    );

    if (referentFirstNameInput) {
      referentFirstNameInput.value = presidentFirstName.value;
    }

    if (referentLastNameInput) {
      referentLastNameInput.value = presidentLastName.value;
    }

    if (referentEmailInput) {
      referentEmailInput.value = presidentEmail.value;
    }
    document
      .querySelectorAll(".js-update_referent")
      .forEach(function (element) {
        element.style.display = "none";
      });
  } else {
    document
      .querySelectorAll(".js-update_referent")
      .forEach(function (element) {
        element.style.display = "block";
      });
    document
      .querySelectorAll(".js-referent_non_president")
      .forEach(function (element) {
        element.style.display = "block";
      });

    cacheFormulaire("#modifierReferent");
  }
});

// verif email president
// const emailInput = document.querySelector(
//   "#campain_association_association_president_user_email"
// );
// // Ajouter un événement 'input' pour détecter les saisies
// emailInput.addEventListener("input", function (event) {
//   const inputValue = event.target.value;
//   console.log(inputValue);

//   const association = document.querySelector(".js-campains").value;
//   console.log(association);

//   $.ajax({
//     url: "/" + association + "/" + inputValue,
//     method: "POST",
//     data: {
//       email: inputValue,
//       association: association,
//     },
//     success: function (response) {
//       console.log(response);

//       // Vérifier si la réponse est un tableau avec une seule valeur
//       if (Array.isArray(response) && response.length === 1) {
//         const isValid = response[0] === 1;
//         console.log(isValid);
//         if (isValid) {
//           // Email unique, masquer le message d'erreur
//           $("#presidentEmailError").hide();
//           $("#submitBtn").prop("disabled", false);
//         } else {
//           // Email non unique, afficher le message d'erreur
//           $("#presidentEmailError").text("L'email n'est pas unique.").show();
//           $("#submitBtn").prop("disabled", true);
//         }
//       } else {
//         console.error("La réponse du serveur est dans un format inattendu.");
//       }
//     },
//     error: function (xhr, status, error) {
//       console.error("Erreur lors de la requête AJAX:", error);
//     },
//   });
// });
