<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Campagne;
use App\Entity\Epargne;
use App\Entity\Groupement;
use App\Entity\MembreSimple;
use App\Entity\ModeCotisation;
use App\Entity\Role;
use App\Entity\Tontine;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fausses données pour alimenter la base.
 * Tous les comptes ont le mot de passe : password
 */
class AppFixtures extends Fixture
{
    private const MOT_DE_PASSE = 'password';

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // --- Rôles ---
        $roles = [];
        foreach (['membre simple', 'trésorier', 'secrétaire', 'admin'] as $nom) {
            $roles[$nom] = (new Role())->setNom($nom);
            $manager->persist($roles[$nom]);
        }

        // --- Groupements, avec leurs membres ---
        $donnees = [
            [
                'nom' => 'Les Femmes de la Médina',
                'admin' => ['Aminata Sow', '77 100 00 01', 'Médina, Dakar'],
                'membres' => [
                    ['Awa Diop', '77 100 00 02', 'Médina, Dakar', 'trésorier'],
                    ['Fatou Ndiaye', '77 100 00 03', 'Médina, Dakar', 'secrétaire'],
                    ['Mariama Ba', '77 100 00 04', 'Gueule Tapée, Dakar', 'membre simple'],
                    ['Khady Fall', '77 100 00 05', 'Fass, Dakar', 'membre simple'],
                    ['Seynabou Gueye', '77 100 00 06', 'Médina, Dakar', 'membre simple'],
                ],
                'epargne' => ['Épargne scolaire 2026', 5000, 600000, 'Fournitures scolaires de la rentrée'],
                'tontine' => ['Tontine du mois', 10000, 6],
                'libres' => ['Khady Fall'],
            ],
            [
                'nom' => 'Union des Femmes de Thiaroye',
                'admin' => ['Rokhaya Mbaye', '77 200 00 01', 'Thiaroye, Pikine'],
                'membres' => [
                    ['Coumba Thiam', '77 200 00 02', 'Thiaroye, Pikine', 'trésorier'],
                    ['Ndèye Faye', '77 200 00 03', 'Thiaroye, Pikine', 'secrétaire'],
                    ['Aïssatou Sy', '77 200 00 04', 'Guinaw Rails, Pikine', 'membre simple'],
                    ['Dieynaba Kane', '77 200 00 05', 'Thiaroye, Pikine', 'membre simple'],
                ],
                'epargne' => ['Caisse de solidarité', 2000, 250000, 'Aide en cas de deuil ou de maladie'],
                'tontine' => ['Tontine de la Tabaski', 15000, 5],
                'libres' => ['Ndèye Faye', 'Dieynaba Kane'],
            ],
        ];

        foreach ($donnees as $g) {
            $groupement = (new Groupement())->setNom($g['nom']);
            $manager->persist($groupement);

            // L'admin du groupement (qui est aussi une membre)
            $utilisateurs = [];
            $admin = new Admin();
            $this->remplir($admin, $g['admin'], $groupement, $roles['admin']);
            $admin->setDateNomination(new \DateTime('2026-01-05'));
            $manager->persist($admin);
            $utilisateurs[] = $admin;

            // Les membres
            foreach ($g['membres'] as [$nomComplet, $tel, $adresse, $fonction]) {
                $membre = new MembreSimple();
                $this->remplir($membre, [$nomComplet, $tel, $adresse], $groupement, $roles[$fonction]);
                $membre->setDateAdhesion(new \DateTime('2026-01-10'));
                $manager->persist($membre);
                $utilisateurs[] = $membre;
            }

            // Une épargne (montant fixe pour tous)
            [$nomEpargne, $montantEpargne, $objectif, $finalite] = $g['epargne'];
            $epargne = (new Epargne())
                ->setObjectif($objectif)
                ->setFinalite($finalite);
            $this->remplirCampagne($epargne, $nomEpargne, $montantEpargne, $groupement, '2026-02-01', '2026-12-31');
            $manager->persist($epargne);

            // Une tontine (certaines membres cotisent librement)
            [$nomTontine, $montantTontine, $tours] = $g['tontine'];
            $tontine = (new Tontine())->setNombreDeTours($tours);
            $this->remplirCampagne($tontine, $nomTontine, $montantTontine, $groupement, '2026-03-01', null);
            $manager->persist($tontine);

            // Affectation des membres aux campagnes (ModeCotisation)
            foreach ($utilisateurs as $utilisateur) {
                $manager->persist($this->affecter($epargne, $utilisateur, false));
                $libre = \in_array($utilisateur->getNomComplet(), $g['libres'], true);
                $manager->persist($this->affecter($tontine, $utilisateur, $libre));
            }
        }

        $manager->flush();
    }

    private function remplir(User $user, array $infos, Groupement $groupement, Role $role): void
    {
        [$nomComplet, $telephone, $adresse] = $infos;
        $login = strtolower(str_replace(' ', '.', $this->sansAccents($nomComplet)));

        $user->setNomComplet($nomComplet)
            ->setTelephone($telephone)
            ->setAdresse($adresse)
            ->setLogin($login)
            ->setGroupement($groupement)
            ->setRole($role);
        $user->setPassword($this->hasher->hashPassword($user, self::MOT_DE_PASSE));
    }

    private function remplirCampagne(Campagne $campagne, string $nom, float $montant, Groupement $groupement, string $debut, ?string $fin): void
    {
        $campagne->setNom($nom)
            ->setMontant($montant)
            ->setDateDebut(new \DateTime($debut))
            ->setDateFin($fin ? new \DateTime($fin) : null)
            ->setGroupement($groupement);
    }

    private function affecter(Campagne $campagne, User $user, bool $libre): ModeCotisation
    {
        return (new ModeCotisation())
            ->setCampagne($campagne)
            ->setUser($user)
            ->setIsLibre($libre);
    }

    private function sansAccents(string $texte): string
    {
        return strtr($texte, [
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'à' => 'a', 'â' => 'a',
            'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ù' => 'u', 'û' => 'u',
            'ç' => 'c', 'È' => 'E', 'É' => 'E',
        ]);
    }
}