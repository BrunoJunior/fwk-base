<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace fwk\utils;

/**
 * Description of Html
 *
 * @author bruno
 */
class Html {

    /**
     * Obtenir un bloc de pagination
     * @param int $nbMilieu
     * @param int $nbPage
     * @param int $page
     * @return string
     */
    public static function getBlocPagination($nbMilieu = 3, $nbPage = 0, $page = 1) {
        $htmlPagination = '';
        if ($nbPage > 1) {
            $debutMilieu = $page - intval($nbMilieu/2);
            $limiteMin = 3;
            $limiteMax = ($nbPage - $nbMilieu - 1);
            $hasSepGauche = $debutMilieu > $limiteMin;
            $hasSepDroit = $debutMilieu < $limiteMax;

            if ($debutMilieu < $limiteMin) {
                $debutMilieu = $limiteMin;
            } elseif ($debutMilieu > $limiteMax) {
                $debutMilieu = $limiteMax;
            }
            $htmlPagination .= '<div class="cov_pag"><nav><ul class="pagination">';
            $htmlPagination .= '<li class="' . ($page <= 1 ? 'disabled' : '') . '"><a href="-1" aria-label="Précédents"><span aria-hidden="true">&laquo;</span></a></li>';
            for ($index = 1; $index <= $nbPage; $index++) {
                if ($hasSepGauche && $index === 2) {
                    $htmlPagination .= '<li class="disabled no-button"><a>...</a></li>';
                }
                if ($index == 1 || $index == 2 && !$hasSepGauche ||
                        $index == $nbPage || $index == $nbPage-1 && !$hasSepDroit ||
                        ($index >= $debutMilieu && $index < ($debutMilieu + $nbMilieu))) {
                    $htmlPagination .= '<li class="' . ($index == $page ? 'active' : '') . '"><a href="' . $index . '">' . $index . '</a></li>';
                }
                if ($hasSepDroit && $index === ($nbPage-1)) {
                    $htmlPagination .= '<li class="disabled no-button"><a>...</a></li>';
                }
            }
            $htmlPagination .= '<li class="' . ($page >= $nbPage ? 'disabled' : '') . '"><a href="+1" aria-label="Suivants"><span aria-hidden="true">&raquo;</span></a></li>';
            $htmlPagination .= '</ul></nav></div>';
        }
        return $htmlPagination;
    }

    /**
     * Get a font awesome icon
     * @param string $name
     * @return string
     */
    public static function getIcon($name, $class = '') {
        if (mb_substr($name, 0, 3) !== 'fa-') {
            $name = 'fa-' . $name;
        }
        return '<i class="fa '.$name.' ' . $class . '"></i>';
    }
}
