// Définition des champs de formulaire pour le référent
const formFieldsReferentNew = document.querySelectorAll(".js_referent_new");
const formFieldsReferentUpdate = document.querySelectorAll(
  ".js_referent_update"
);

const infoFieldsReferent = document.querySelector(".js-referent_info");
const buttonsFieldsReferent = document.querySelector(".js-referent_button");

const hiddenFieldRefId = document.querySelector("input[name='refId']");

const formFieldsPresident = document.querySelectorAll(".js-president");
let presidentIsReferentCheckbox = document.querySelector(
  "input[id=campain_association2_presidentIsReferent]"
);

// chargement de la page
document.addEventListener("DOMContentLoaded", function () {
  const isPresidentReferent = document.querySelector(
    "input[name='isPresidentReferent']"
  );
  console.log("chargement page : ");

  // le referent est aussi le president
  if (isPresidentReferent.value !== "") {
    cacheFormulaire("#modifierReferent");
    cacheFormulaire("#createReferentForm");
    infoFieldsReferent.style.display = "none";
    buttonsFieldsReferent.style.display = "none";
    activerDesactiverChamps(formFieldsReferentNew, true);
  }
  // il y a dejà un referent et on ne modifie rien
  else if (hiddenFieldRefId.value !== 0) {
    activerDesactiverChamps(formFieldsReferentNew, true);
  }
});

// Fonction pour afficher/cacher un formulaire et masquer l'autre
const afficherCacheFormulaire = (formulaireAAfficher, formulaireAMasquer) => {
  afficherFormulaire(formulaireAAfficher);
  cacheFormulaire(formulaireAMasquer);
};
// Fonction pour afficher un formulaire et masquer l'autre
const afficherFormulaire = (formulaireAAfficher) => {
  document.querySelector(formulaireAAfficher).style.display = "block";
};
// Fonction pour cacher formulaire
const cacheFormulaire = (formulaireAMasquer) => {
  document.querySelector(formulaireAMasquer).style.display = "none";
};
// Fonction pour activer ou désactiver les champs d'un formulaire
const activerDesactiverChamps = (formFields, disable) => {
  formFields.forEach(function (field) {
    if (disable) {
      field.setAttribute("disabled", "disabled");
    } else {
      field.removeAttribute("disabled");
    }
  });
};

// Fonction pour modifier le président
const modifierPresident = (evt) => {
  evt.preventDefault();
  afficherCacheFormulaire("#modifierPresidentForm", "#createPresidentForm");
  activerDesactiverChamps(formFieldsPresident, true);
};

// Fonction générique pour ajouter un nouveau président ou référent
const ajouterNouveauRole = (evt, formId, formFields, modifierFormId) => {
  evt.preventDefault();
  afficherCacheFormulaire(formId, modifierFormId);
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
const ajouterReferent = (evt) => {
  evt.preventDefault();
  presidentIsReferentCheckbox.checked = false;
  cacheFormulaire("#modifierReferent");
  afficherFormulaire("#createReferentForm");
  activerDesactiverChamps(formFieldsReferentNew, false);
};
// Fonction pour modifier le référent
const modifierReferent = (evt) => {
  evt.preventDefault();
  console.log("modifierReferent");
  afficherFormulaire("#modifierReferent");
  cacheFormulaire("#createReferentForm");
  presidentIsReferentCheckbox.checked = false;

  activerDesactiverChamps(formFieldsReferentNew, true);
};

// Écouteur d'événement pour la case à cocher "Président(e) est Référent(e)"
presidentIsReferentCheckbox.addEventListener("change", () => {
  // case cochée
  if (presidentIsReferentCheckbox.checked) {
    cacheFormulaire("#modifierReferent");
    cacheFormulaire("#createReferentForm");

    infoFieldsReferent.style.display = "none";
    buttonsFieldsReferent.style.display = "none";
    activerDesactiverChamps(formFieldsReferentNew, true);
  } else {
    infoFieldsReferent.style.display = "block";
    buttonsFieldsReferent.style.display = "block";
    activerDesactiverChamps(formFieldsReferentNew, false);
    // // il n'y a pas encore de referent, on affiche automatiquement creation nouveau
    if (hiddenFieldRefId.value == 0) {
      afficherFormulaire("#createReferentForm");
    }
  }
});
