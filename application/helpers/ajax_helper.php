<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}

?>