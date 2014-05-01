<?php /* Smarty version Smarty-3.1.12, created on 2014-05-01 08:07:13
         compiled from "smarty\templates\matchobserver\matchlist.tpl" */ ?>
<?php /*%%SmartyHeaderCode:327605354d42fa6f866-94571428%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '76d86c3dd18b0d405692dfb9183a21e3fe281303' => 
    array (
      0 => 'smarty\\templates\\matchobserver\\matchlist.tpl',
      1 => 1398931627,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '327605354d42fa6f866-94571428',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5354d42fa73766_84810320',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5354d42fa73766_84810320')) {function content_5354d42fa73766_84810320($_smarty_tpl) {?><table id="matchlist" class="table">
    <thead>
        <tr>
            <td>
                Kampdato
            </td>
            <td>
                Kamp
            </td>
                 
            <td style="text-align:center">
                <a href="javascript:void(0);" onmouseover="return overlib('Odds tilgjengelig', WIDTH, 100)" onmouseout="return nd();">O</a>
            </td>    
            <td style="text-align:center">
                <a href="javascript:void(0);" onmouseover="return overlib('Derby', WIDTH, 75);" onmouseout="return nd();">D</a>
            </td>
            <td style="text-align:center">
                <a href="javascript:void(0);" onmouseover="return overlib('Ekstremvær', WIDTH, 75);" onmouseout="return nd();">E</a>
            </td>
            <!--
            <td>
                Lag
            </td>
            <td>
                Tropp
            </td>
            -->
            <td style="text-align:center">
                <a href="javascript:void(0);" onmouseover="return overlib('Underlag mismatch', WIDTH, 140);" onmouseout="return nd();">U</a>
            </td>
            <td style="text-align:center">
                <a href="javascript:void(0);" onmouseover="return overlib('Antall suspensjoner totalt', WIDTH, 150);" onmouseout="return nd();">S</a>
            </td>
        </tr>
    </thead>
    <tbody id="matchlist_body">
    </tbody>
</table><?php }} ?>