<?php /* Smarty version Smarty-3.1.12, created on 2013-10-03 21:33:46
         compiled from "smarty\templates\login.tpl" */ ?>
<?php /*%%SmartyHeaderCode:34455242e9a7c783e3-44440766%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '403851c49ec8d5ed21288a7d0f969afa0c0aee93' => 
    array (
      0 => 'smarty\\templates\\login.tpl',
      1 => 1380836021,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '34455242e9a7c783e3-44440766',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5242e9a7cb0ee8_11600470',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5242e9a7cb0ee8_11600470')) {function content_5242e9a7cb0ee8_11600470($_smarty_tpl) {?><head>
    <meta charset="utf-8">
    <title>MatchObserver Login</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css" />
    <link href="favicon.ico" rel="icon" type="image/x-icon" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script src="http://www.modernizr.com/downloads/modernizr-latest.js"></script>
    <script type="text/javascript" src="js/admin.js"></script>
</head>
<<?php ?>?php session_start(); ?<?php ?>>
<body>
    <form id="slick-login" action="admin.php" method="post">
        <label for="username">username</label><input id="username" type="text" name="username" class="placeholder" placeholder="e-post">
        <label for="password">password</label><input id="password" type="password" name="password" class="placeholder" placeholder="passord">
        <input type="submit" value="Logg inn"/>
    </form>
</body>
</html><?php }} ?>