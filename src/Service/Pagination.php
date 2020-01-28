<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Pagination
{
    // Variables à définir dans le controller

    /**
     * Nom de l'entité sur à paginer
     * @var string
     */
    private $entityClass;

    /**
     * Nombre d'affichage par page
     * @var int
     */
    private $limit = 10;

    /**
     * Page sur laquelle on se trouve actuellement
     * @var int
     */
    private $currentPage = 1;

    // Variables du constructor
    /**
     * Manager de doctrine
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Moteur de template twig
     * @var Environment
     */
    private $twig;

    /**
     * Nom de la route à utuliser dans les boutons de la pagination
     * @var mixed
     */
    private $route;

    /**
     * Chemin vers le template de pagination (définit par défaut dans le service.yaml
     * @var string
     */
    private $templatePath;

    /**
     * Pagination constructor.
     * @param EntityManagerInterface $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->manager = $manager;
        $this->twig = $twig;
        // Récupération du nom de la route dans la requète actuelle
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;
    }

    /**
     * Permet de rendre l'affichage de la pagination
     */
    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    /**
     * Permet de récupérer le nombre total de page sur l'entité
     * @return false|float
     * @throws \Exception // Si l'entityClass n'est pas configuré
     */
    public function getPages()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet pagination");
        }
        // 1) Connaitre le total des enregistrement de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        // 2) faire la division, l'arrondi et le ren
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    /**
     * Permet de récuperer les données paginées de l'entité
     * @return object[]
     * @throws \Exception // Si l'entityClass n'est pas configuré
     */
    public function getData()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet pagination");
        }
        // 1) Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // 2) Demander au repository de trouver les élements
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        // 3) Renvoyer les élements en question
        return $data;
    }

    ////////// Getters and Setters

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }



    /**
     * @param mixed $entityClass
     * @return Pagination
     */
    public function setEntityClass($entityClass): Pagination
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * @param int $limit
     * @return Pagination
     */
    public function setLimit(int $limit): Pagination
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $currentPage
     * @return Pagination
     */
    public function setCurrentPage(int $currentPage): Pagination
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * @param mixed $route
     * @return Pagination
     */
    public function setRoute($route): Pagination
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @param mixed $templatePath
     * @return Pagination
     */
    public function setTemplatePath($templatePath): Pagination
    {
        $this->templatePath = $templatePath;

        return $this;
    }

}