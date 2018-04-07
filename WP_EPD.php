<?php
/*
  Plugin Name: Exquisite PayPal Donation
  Version: v2.0.0
  Plugin URI: http://plugins.dgcult.com/exq
  Author: DgCult
  Author URI: http://plugins.dgcult.com/exq
  Description: Add an extensive, easily configurable, PayPal donation form as a widgit or a shortcode
  License: GPL2
 */

/*
 * Table Of Contents:*********************************************************
 *
 * 1.constants
 * 2.Add options
 * 3.the form
 * 4.option page
 * 5.widget
 * 6.Init
 *
 *
 * ***************************************************************************
 */


//==========================Constants=======================================//
define('Exq_ppd_VERSION', '1.0.1');
define('Exq_ppd_PLUGIN_URL', plugins_url('', __FILE__));
//=========================================================================//


//==========================Add=Options=======================================//
function Exq_ppd_plugin_install() {
    // Some default options
    add_option('Exq_ppd_payment_email', get_bloginfo('admin_email'));
    add_option('Exq_ppd_default_currency', 'USD');
    add_option('Exq_ppd_amount_field', true);
    add_option('Exq_ppd_fixed_field', false);
    add_option('Exq_ppd_currency_dropdown', 1);
    add_option('Exq_ppd_currencies', array('USD','EUR'));
    add_option('Exq_ppd_button', 'image-1');
    add_option('Exq_ppd_frame_back', 'white');
    add_option('Exq_ppd_form_title', 'Donate Securely with PayPal');
    add_option('Exq_ppd_return_url', home_url());
}

register_activation_hook(__FILE__, 'Exq_ppd_plugin_install');
//=========================================================================//




//==========================The=Form=======================================//

function Exq_ppd_the_form($type) {

   $title = get_option('Exq_ppd_form_title');
   $email =  get_option('Exq_ppd_payment_email');
   $dropdown = get_option('Exq_ppd_currency_dropdown');
   ($dropdown == 1)? $select = true :  $select = false;
   $currencies = get_option('Exq_ppd_currencies');
   $default_currency = get_option('Exq_ppd_default_currency');
    if(!$select) {$defCur = " (".$default_currency.")<br/>"; $amountClass = "no-margin";}else{$defCur="";$amountClass = "margin";}
   $image = get_option('Exq_ppd_button');
    $back = get_option('Exq_ppd_frame_back');
    $border = get_option('Exq_ppd_frame_border');
    ($type)? $class=' Exq_ppd_widgit':$class=' Exq_ppd_else';
    $url = get_option('Exq_ppd_return_url');



        //form starts
    $form = "<form class='Exq_ppd-form{$class}' action='https://www.paypal.com/cgi-bin/webscr' method='post'";
             if($back || $border) $form .= "style='";
             if($back) $form .= "background:{$back};";
             if($border) $form .= "border:2px solid {$border}'";
             if($back || $border) $form .= "'";
    $form .=   ">";
        if(!$type) $form .= "<h3>{$title}</h3>";
    $form .="<p><input name='business' type='hidden'  value='{$email}'> <input name='cmd' type='hidden' value='_donations'></p>";

        //Print currency dropdown?

        if($select){

            $form .= "<p>".__('Choose currency')."<select name='currency_code'>";


            $temp = Exq_ppd_get_currency_name($default_currency);
            $form .= "<option value='{$default_currency}'>".$temp."</option>";

            foreach ($currencies AS $currency) {
                if($currency !== $default_currency){
                $temp = Exq_ppd_get_currency_name($currency);
                $form .= "<option value='{$currency}'>".$temp."</option>";
                }
            }


            $form .= "</select></p>";

            //No dropdown, print default currency
        }else{

            $form .= "<input name='currency_code' type='hidden' value='{$default_currency}'> ";

        }

    $form .= "<p>Enter amount{$defCur} <input id='amount' class='{$amountClass}' name='amount' pattern='[0-9]*' required='' type='text'>";

        ($select)? "<span>Exq_ppd_get_currency_name($default_currency)</span>" : "";
    $form .= "<INPUT TYPE='hidden' NAME='return' value='{$url}'>";
    $form .= "<br></p><input name='lc' type='hidden' value='".Exq_ppd_get_system_lang()."'>
        <p><button id='exq_button' class='{$image}'  name='submit' type='image'></button></p>
        </form>";

    return $form;


}

add_shortcode('Exq_ppd_form', 'Exq_ppd_the_form');



//***********Get language(25)**************

function Exq_ppd_get_system_lang(){

    $lang = get_bloginfo('language');

    $prefixedLangs = array('da_DK','he_IL','id_ID','ja_JP','no_NO','pt_BR','ru_RU','sv_SE','th_TH','tr_TR','zh_CN','zh_HK','zh_TW');
    $unPrefixedLangs = array('AU','AT','BE','BR','CA','CH','CN','DE','ES','GB','FR','IT','NL','PL','PT','RU','US');


    if(in_array($lang, $prefixedLangs)){
        return $lang;
    }else{
         $lang = explode('-', $lang);
         $lang = $lang[1];
        if(in_array($lang, $unPrefixedLangs)){
            return $lang;
        } else{
            return "US";
        }
    }
}


//***********Get currency(22)**************


$ExqAllCurencies = array('USD','EUR','AUD','CAD','CZK','DKK','HKD','HUF','JPY','NOK','NZD','PLN','GBP','SGD','SEK','CHF','SGD','RUB','PHP','MXN','ILS');


function Exq_ppd_get_currency_name($currency){

    switch ($currency) {

        case 'USD':
            $currencyName = __('U.S. Dollar');
            break;

        case 'AUD':
            $currencyName = __('Australian Dollar');
            break;

        case 'CAD':
            $currencyName = __('Canadian Dollar');
            break;

        case 'CZK':
            $currencyName = __('Czech Koruna');
            break;

        case 'DKK':
            $currencyName = __('Danish Krone');
            break;

        case 'EUR':
            $currencyName = __('Euro');
            break;

        case 'HKD':
            $currencyName = __('Hong Kong Dollar');
            break;

        case 'HUF':
            $currencyName = __('Hungarian Forint');
            break;

        case 'JPY':
            $currencyName = __('Japanese Yen');
            break;

        case 'NOK':
            $currencyName = __('Norwegian Krone');
            break;

        case 'NZD':
            $currencyName = __('New Zealand Dollar');
            break;

        case 'PLN':
            $currencyName = __('Polish Zloty');
            break;

        case 'GBP':
            $currencyName = __('Pound Sterling');
            break;

        case 'SGD':
            $currencyName = __('Singapore Dollar');
            break;

        case 'SEK':
            $currencyName = __('Swedish Krona');
            break;

        case 'CHF':
            $currencyName = __('Swiss Franc');
            break;

        case 'SGD':
            $currencyName = __('Singapore Dollar');
            break;

        case 'RUB':
            $currencyName = __('Russian Ruble');
            break;

        case 'PHP':
            $currencyName = __('Philippine Peso');
            break;

        case 'MXN':
            $currencyName = __('Mexican Peso');
            break;

        case 'ILS':
            $currencyName = __('Israeli New Sheqel');
            break;


    }


    return $currencyName;

}



//=========================================================================//



//==========================Option=Page=======================================//

function Exq_ppd_option_page() {
    global $ExqAllCurencies;



    if (isset($_POST['info_update'])) {
        echo '<div id="message" class="updated fade"><p><strong>';



        update_option('Exq_ppd_payment_email', $_POST["Exq_ppd_payment_email"]);
        update_option('Exq_ppd_form_title', $_POST["Exq_ppd_form_title"]);
        update_option('Exq_ppd_return_url',  $_POST["Exq_ppd_return_url"]);
        update_option('Exq_ppd_default_currency', $_POST["Exq_ppd_default_currency"]);
        update_option('Exq_ppd_currency_dropdown', ($_POST['Exq_ppd_currency_dropdown'] == '1') ? '1' : '-1' );
        if($_POST["Exq_ppd_currencies"]) update_option('Exq_ppd_currencies', $_POST["Exq_ppd_currencies"]);
        update_option('Exq_ppd_frame', ($_POST['Exq_ppd_frame'] == '1') ? '1' : '-1' );
        update_option('Exq_ppd_frame_back',  $_POST["Exq_ppd_frame_back"]);
        update_option('Exq_ppd_frame_border',  $_POST["Exq_ppd_frame_border"]);
        update_option('Exq_ppd_button',  $_POST["Exq_ppd_button"]);

        //update_option('Exq_ppd_currencies', array('USD','EUR'));



        echo 'Options Updated!';
        echo '</strong></p></div>';
    }

    $selectedCurrencies = get_option('Exq_ppd_currencies');
    $exqButton = get_option('Exq_ppd_button');

?>
    <style>
        #footer-thankyou, #footer-upgrade {display: none; }
        #post-body h2 {  font-size: 220%;
            font-weight: bold;
            color: #2ea2cc;
            margin-bottom: 35px;}
    </style>
<div class=wrap>
    <div id="poststuff"><div id="post-body">


        <h2><?php _e('Exquisite PayPal Donation v ');echo Exq_ppd_VERSION; ?></h2>
    <div style="background: none repeat scroll 0 0 #ECECEC;border: 1px solid #CFCFCF;color: #363636;margin: 10px 0 15px;padding:15px;text-shadow: 1px 1px #FFFFFF;">
        <?php _e('Check our web page for more information and live examples: ')?><a href="http://plugins.dgcult.com/exq" target="_blank" >Exquisite PayPal Donation</a><br/>
        <?php _e('Your input, comments, suggestions and requests for support are most welcome: ')?> <a href="http://plugins.dgcult.com/plugins/contact" target="_blank" >contact us</a>
    </div>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <input type="hidden" name="info_update" id="info_update" value="true" />

        <div class="postbox">
            <h3><label for="title"><?php _e('Plugin Usage')?></label></h3>
            <div class="inside">
                <p><?php _e('There are 3 ways you can use this plugin:')?></p>
                <ol>
                    <li><?php _e('Configure the options below and then add the shortcode <strong>[Exq_ppd_form]</strong> to a post or page (where you want the payment form to be displayed)')?></li>
                    <li><?php _e('Call the function from a template file:')?> <strong>&lt;?php echo Exq_ppd_the_form(); ?&gt;</strong></li>
                    <li><?php _e('Use the <strong>Exquisite PayPal Donation</strong> Widget from the Widgets menu')?></li>
                    </p><p><strong style="color: #2ea2cc; margin-top:  20px; font-size: 110%;"><?php _e('Receiving different currencies in your PayPal account can result in unexpected conversion fees. Manage your currencies in your PayPal account "manage currencies" page.')?></strong></p>
                </ol>
            </div></div>

    <div class="postbox">
    <h3><label for="title"><?php _e('Exquisite PayPal Donation Plugin Option')?></label></h3>
    <div class="inside">

    <table class="form-table">


<!--//****************Paypal mail************************//-->

    <tr valign="top"><td width="25%" align="left">
            <strong><?php _e('Paypal Email address:')?></strong>
        </td><td align="left">
            <input name="Exq_ppd_payment_email" type="text" size="35" value="<?php echo get_option('Exq_ppd_payment_email'); ?>"/>
            <br /><i><?php _e('Your Paypal account registered Email address')?></i><br/>
        </td></tr>

        <!--//****************Form title************************//-->

        <tr valign="top"><td width="25%" align="left">
                <strong><?php _e('Form title:')?></strong>
            </td><td align="left">
                <input name="Exq_ppd_form_title" type="text" size="35" value="<?php echo get_option('Exq_ppd_form_title'); ?>"/>
                <br /><i><?php _e('The title that will be represented at the head of the donation form')?></i><br/>
            </td></tr>

<!--//****************Return url************************//-->

    <tr valign="top"><td width="25%" align="left">
            <strong><?php _e('Return URL from PayPal:')?></strong>
        </td><td align="left">
            <input name="Exq_ppd_return_url" type="text" size="60" value="<?php echo get_option('Exq_ppd_return_url'); ?>"/>
            <br /><i><?php _e('Enter a return URL (could be a Thank You page). PayPal will redirect visitors to this page after a successful transaction.')?></i><br />
        </td></tr>

<!--//****************Default currency************************//-->


    <tr valign="top"><td width="25%" align="left">
            <strong><?php _e('Default Payment Currency </strong><br/><i>(Will be used when not displaying drop down or as the first option in drop down selection)</i>:')?>
        </td><td align="left">
            <select name="Exq_ppd_default_currency">
                <?php
                foreach($ExqAllCurencies as $item){
                    ?>

                    <option value="<?= $item ?>" <?php if($item == get_option('Exq_ppd_default_currency') ){echo " selected "; } ?>><?php echo Exq_ppd_get_currency_name($item) ?></option>
                <?php
                }
                ?>
            </select>
            <br/><i><?php _e('Your PayPal donation form default currency.')?></i><br />
        </td></tr>


<!--//****************Show dropdown?************************//-->

    <tr valign="top"><td width="25%" align="left">
            <strong><?php _e('Show DropDown:')?></strong>
        </td><td align="left">
            <input name="Exq_ppd_currency_dropdown" type="checkbox"<?php if (get_option('Exq_ppd_currency_dropdown') != '-1') echo ' checked="checked"'; ?> value="1"/>
            <i> <?php _e('Use a drop down select field to allow different currencies choice for your clients')?> </i>
        </td></tr>

<!--//****************currencies multiple selection************************//-->

    <tr valign="top"><td width="25%" align="left">
            <strong><?php _e('Choose Payment Currency: <br/><i>(use control key for multiple selection)</i>')?></strong>
        </td><td align="left">
            <select multiple id="Exq_ppd_currencies"  name="Exq_ppd_currencies[]">
                <?php
                foreach($ExqAllCurencies as $item){
                    ?>
                    <option value='<?= $item ?>' <?php if(in_array($item, $selectedCurrencies)) echo " selected "; ?>><?php echo Exq_ppd_get_currency_name($item) ?></option>
                    <?php
                }
                ?>
            </select>
            <br/><i><?php _e('These are the currencies your visitors can choose from.')?></i><br />
        </td></tr>



<!--//****************Frame color************************//-->

    <tr valign="top"><td width="25%" align="left">
            <strong><?php _e('Frame Background color:')?></strong>
        </td><td align="left">
            <input name="Exq_ppd_frame_back" class="color-field" type="text" value="<?= get_option('Exq_ppd_frame_back') ?>"/>
            <i> <?php _e('The background color of your donation form, no background color by default')?> </i>
        </td></tr>

<!--//****************Frame border color************************//-->

    <tr valign="top"><td width="25%" align="left">
            <strong><?php _e('Frame Border color:')?></strong>
        </td><td align="left">
            <input name="Exq_ppd_frame_border" type="text" class="color-field" value="<?= get_option('Exq_ppd_frame_border') ?>"/>
            <i> <?php _e('The border color of your donation form, no border by default')?> </i>
        </td></tr>


<!--//****************Button image************************//-->

    </table>

        <br /><br />
        <strong><?php _e('Choose a Donate Button Type :')?></strong>
        <br /><i><?php _e('This is the button your visitors will click on.')?></i><br />
        <table style="width:50%; border-spacing:0; padding:0; text-align:center;">
            <tr>
                <td>
                    <?php _e('<input type="radio" name="Exq_ppd_button" value="image-1"') ?>
                    <?php if ($exqButton == 'image-1') echo " checked " ?>
                    <?php _e('/>') ?>
                </td>
                <td>
                    <?php _e('<input type="radio" name="Exq_ppd_button" value="image-2"') ?>
                    <?php if ($exqButton == "image-2") echo " checked " ?>
                    <?php _e('/>') ?>
                </td>
                <td>
                    <?php _e('<input type="radio" name="Exq_ppd_button" value="image-3"') ?>
                    <?php if ($exqButton == "image-3") echo " checked " ?>
                    <?php _e('/>') ?>
                </td>
            </tr>
            <tr>
                <td><img border="0" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_92x26.png" alt="" /></td>
                <td><img border="0" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_cc_147x47.png" alt="" /></td>
                <td><img border="0" src="https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_pp_142x27.png" alt="" /></td>
            </tr>
        </table>

    </div></div><!-- end of postbox -->

    <div class="submit">
        <input type="submit" class="button-primary" name="info_update" value="<?php _e('Update options'); ?> &raquo;" />
    </div>
</form>

        </div></div> <!-- end of .poststuff and post-body -->
</div><!-- end of .wrap -->
</div>

    <!--//****************END************************//-->
<?php
}



// Displays Options menu
function Exq_ppd_add_option_pages() {
    if (function_exists('add_options_page')) {
        add_options_page('Exquisite PayPal Donation', 'Exquisite PayPal Donation', 'manage_options', 'Exquisite-PayPal-Donation', 'Exq_ppd_option_page');
    }
}

// Insert the Exq_ppd_add_option_pages in the 'admin_menu'
add_action('admin_menu', 'Exq_ppd_add_option_pages');

//=========================================================================//




//==========================Init=======================================//

function Exq_ppd_init() {
   wp_register_style('Exq_ppd-styles', Exq_ppd_PLUGIN_URL . '/exq-ppd.css');
    wp_enqueue_style('Exq_ppd-styles');



///   //Widget code

    function Exq_ppd_widget($args) {
        extract($args);

        echo $before_widget;
        echo $before_title .get_option('Exq_ppd_form_title').$after_title;
        echo Exq_ppd_the_form('widgit');
        echo $after_widget;
    }

    function Exq_ppd_widget_control() {
        ?>
        <p>
            <? _e("Set the Plugin Settings from the Settings menu"); ?>
        </p>
    <?php
    }


   $widget_options = array('classname' => 'widget_Exq_ppd', 'description' => __("Display Exquisite PayPal Donation"));
    wp_register_sidebar_widget('Exq_ppd_widget', __('Exquisite PayPal Donation'), 'Exq_ppd_widget', $widget_options);
   wp_register_widget_control('Exq_ppd_widget', __('Exquisite PayPal Donation'), 'Exq_ppd_widget_control');
}


function Exq_ppd_color_picker() {

    if( is_admin() ) {

        // Add the color picker css file
        wp_enqueue_style( 'wp-color-picker' );

        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'custom-script-handle', plugins_url( 'actions.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    }
}

add_action('init', 'Exq_ppd_init');
add_action( 'admin_enqueue_scripts', 'Exq_ppd_color_picker' );

//=========================================================================//