<?php

namespace App\Service;

use App\Entity\Campains;
use App\Entity\Association;
use Symfony\Component\Mime\Email;
use App\Entity\CampainAssociation;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CampainEmailSender
{

    public function __construct(
        private MailerInterface $mailer,
        private AssociationRepository $assoRepo,
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    private function createEmail(Campains $campain, ?String $tokenPersonnalise): Email
    {

        $texteEmail = str_replace(
            '[Lien_personnalisé_ici_ne_pas_modifier]',
            $this->urlGenerator->generate(
                'associationhh',
                ['token' => $tokenPersonnalise]
            ),
            $campain->getTexteEmail()
        );
        // Créer l'e-mail
        $email = (new Email())
            ->from($campain->getEmailFrom())
            ->subject($campain->getObjetEmail())
            ->html($texteEmail);

        if ($campain->getEmailCc() !== null) {
            $email->cc($campain->getEmailCc());
        }

        return $email;
    }
    /**
     * Lien personnalisé
     *
     * @param Association $association
     * @return string
     */
    private function genererLienPersonnalise(Association $association): string
    {
        // Générer une chaîne de caractères aléatoire
        return bin2hex(random_bytes(16)); // Génère une chaîne hexadécimale de 16 octets aléatoires
    }

    public function sendEmailToDestinataires(Campains $campain)
    {
        // Récupérer les destinataires sélectionnés de la campagne
        $destinataires = $campain->getDestinataire();
        $emailsDestinataires = [];
        // die;
        // Vérifier s'il y a des destinataires sélectionnés
        if (!empty($destinataires)) {
            // liste des emails
            foreach ($destinataires as $destinataire) {
                if ($destinataire === "presidents") {
                    $emailsPresidents = $this->assoRepo->findAllAssociationPresidentEmail();
                }
                if ($destinataire === "referents") {
                    $emailsReferents = $this->assoRepo->findAllAssociationReferentEmails();
                }
            }
            // liste de toutes les associations
            $listeAsso = $this->assoRepo->findAll();
            $listeEmails = "";
            foreach ($listeAsso as $association) {
                // Créer l'e-mail contenenant le token personnalisé pour l'association
                $token = $this->genererLienPersonnalise($association);
                $email = $this->createEmail($campain, $token);
                $destinataire = "";
                if (in_array('presidents', $destinataires)) {
                    $destinataire = $emailsPresidents[$association->getId()];
                }
                if (in_array('referents', $destinataires)) {
                    if (isset($emailsReferents[$association->getId()])) {
                        $destinataire .= ', ' . $emailsReferents[$association->getId()];
                    }
                }

                $this->createCampainAssociation($campain, $destinataire, $association, $token);

                // Envoyer l'e-mail à chaque destinataire sélectionné
                // $email->to($destinataire);
                $email->to('maryonete26@gmail.com');
                $listeEmails .= $destinataire . ', ';
                // $this->mailer->send($email);

            }
            // Envoyer un e-mail récapitulatif
            //$email = $this->createEmail($campain);
            //$this->createEmailRecap($email, $listeEmails, $campain->getEmailCc());
        }
    }
    private function createCampainAssociation(
        Campains $campain,
        string $email,
        Association $asso,
        String $token
    ): void {
        $campainAsso = $this->entityManager->getRepository(
            CampainAssociation::class
        )->findOneBy([
            'association' => $asso,
            'campains' => $campain,
        ]);

        // Si une entité existante est trouvée, mettez à jour ses valeurs
        if ($campainAsso === null) {
            $campainAsso = new CampainAssociation();
        }
        $campainAsso->setEmails($email)
            ->setStatut('send')
            ->setSendAt(new \DateTime())
            ->setCampains($campain)
            ->setToken($token)
            ->setAssociation($asso);
        // Assurez-vous que votre entité CampainAssociation a une relation appropriée avec la campagne.
        // Puis, vous pouvez ajouter $campainAsso à la campagne :
        $campain->addCampainAssociation($campainAsso);
        $this->entityManager->persist($campainAsso);
        $this->entityManager->flush();
    }
    private function createEmailRecap(Email $email, String $listeDestinataires, ?String $cc = null)
    {
        # TODO
        //     $getTexteEmail = $campain->getTexteEmail();
        //     if ($campain->getEmailCc() !== null) {
        //         $getTexteEmail .= " <hr> Email envoyé en copie à : " . $campain->getEmailCc();
        //     }
        //     $getTexteEmail .= " <hr> <b>Destinataires : </b>" . $listeEmails;

        //     $emailRecap = (new Email())
        //         ->from($campain->getEmailFrom())
        //         ->subject("EMAIL REACP - " . $campain->getObjetEmail())
        //         ->to('maryonete26@gmail.com')
        //         ->html($getTexteEmail);
        //     dump($emailRecap);
        //     // die;
        //     $this->mailer->send($emailRecap);
        // } catch (\Exception $e) {
        //     $errorMessage = $e->getMessage();
        //     dump(
        //         'Une erreur s\'est produite lors de l\'envoi de l\'e-mail récapitulatif : ' . $errorMessage
        //     );
    }
    private function sendEmail(Email $email, string $recipient): void
    {
        $email->to($recipient);
        // Envoyer l'e-mail
        $this->mailer->send($email);
    }
}
