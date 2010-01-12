<?php
class Zend_View_Helper_CleanInfoProduct
{
    function cleanInfoProduct($string)
    {
        $sep = '#ROW#';
        $ignore = '#IGNORE#';
        $tagBloc = 'p';

        $tagTitle = array('h3', 'h2', 'table', 'tr', 'td', 'tbody', 'thead');

        // on rajoute un retour à la ligne après tous les titres
        foreach($tagTitle as $tag) {
            $string = str_ireplace("</".$tag.">", "</".$tag.">\r\n", $string);
            $string = str_ireplace('<'.$tag, $ignore.'<'.$tag, $string);
            $string = str_ireplace('</'.$tag.'>', $ignore.'</'.$tag.'>', $string);
        }

        // on supprime tous les retours à la ligne (s'il y en a plusieurs, il les remplace qu'une fois)
        $str = preg_replace("/(\r\n)+|(\n|\r)+/", $sep, $string);

        // on récupère chaque bloc dans un tableau
        $tab_str = explode($sep, $str);
        $retour = '';

        foreach($tab_str as $str)
        {
            $str = trim($str);

            if( strlen($str) > 0 && $str != '<br>') 
            {
                if( substr($str, 0, strlen($ignore) ) != $ignore ) 
                {
                    $retour .= '<'.$tagBloc.'>' . $str . '</'.$tagBloc.'>';
                }
                else
                {
                    $retour .= $str;
                }
            }
        }

        $retour = str_replace($ignore, '', $retour);

        return $retour;
    }
}