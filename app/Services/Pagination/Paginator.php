<?php


namespace App\Services\Pagination;


use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\Request;

class Paginator
{
    const MAX_PER_PAGE = 10;

    /** @var string */
    private $url;

    /** @var int */
    private $intMaxPerPage;

    /**
     * @var bool
     */
    private $usePagination;


    public function __construct(int $intMaxPerPage, bool $usePagination)
    {
        $this->intMaxPerPage = (!is_null($intMaxPerPage) && !empty($intMaxPerPage)) ? $intMaxPerPage : self::MAX_PER_PAGE;
        $this->usePagination = $usePagination;
    }

    /**
     * @param array $data
     * @param Request $request
     * @return array
     */
    public function paginate(array $data, Request $request): array
    {
        $this->url = explode("?", $request->getUri())[0];
        $adapter = new ArrayAdapter($data);
        $pagerfanta = new Pagerfanta($adapter);


        //-- Check if necessary use pagination
        if ($this->isUsePagination($request, $pagerfanta)) {
            return [
                "data" => $this->getPaginationDataInfo($pagerfanta, $data),
                "links" => $this->getPaginationLinksInfo($pagerfanta),
                "meta" => $this->getPaginationMetaInfo($pagerfanta),
            ];
        } else {
            return $data;
        }
    }

    /**
     * @param Request $request
     * @param $pagerfanta
     * @return bool|mixed
     */
    private function isUsePagination(Request $request, &$pagerfanta): bool
    {
        $page = $request->query->get("page");
        $limit = $request->query->get("limit");

        if (!is_null($page) || !is_null($limit)) {
            if (!is_null($limit)) {
                $pagerfanta->setMaxPerPage($limit);
                $this->intMaxPerPage = $limit;
            }
            if (!is_null($page)) {
                $pagerfanta->setCurrentPage($this->getCurrentPage($request));
            }
            return true;
        } else {
            return $this->usePagination;
        }
    }

    /**
     * Get current pagination page
     * @param Request $request
     * @return int
     */
    private function getCurrentPage(Request $request): int
    {
        $page = $request->query->get('page');
        if (!is_null($page)) {
            return (int)$page;
        }
        return 1;
    }

    /**
     * @param Pagerfanta $pagerfanta
     * @return array
     */
    private function getPaginationLinksInfo(Pagerfanta $pagerfanta): array
    {
        return [
            "first" => $this->url . "?page=1",
            "last" => $this->url . "?page=" . $pagerfanta->getNbPages(),
            "prev" => $pagerfanta->hasPreviousPage() ? $this->url . "?page=" . $pagerfanta->getPreviousPage() : null,
            "next" => $pagerfanta->hasNextPage() ? $this->url . "?page=" . $pagerfanta->getNextPage() : null,
        ];
    }

    private function getPaginationDataInfo(Pagerfanta $pagerfanta, array $data): array
    {
        return array_slice($data, ($pagerfanta->getCurrentPageOffsetStart() - 1), $this->intMaxPerPage);
    }

    /**
     * @param Pagerfanta $pagerfanta
     * @return array
     */
    private function getPaginationMetaInfo(Pagerfanta $pagerfanta): array
    {
        return [
            "current_page" => $pagerfanta->getCurrentPage(),
            "from" => $pagerfanta->getCurrentPageOffsetStart(),
            "last_page" => $pagerfanta->getNbPages(),
            "path" => $this->url,
            "per_page" => $pagerfanta->getMaxPerPage(),
            "to" => $pagerfanta->getCurrentPageOffsetEnd(),
            "total" => $pagerfanta->count(),
        ];
    }


}