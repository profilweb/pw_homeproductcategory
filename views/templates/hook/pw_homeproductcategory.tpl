{**
 * Products from one category on HomePage: module for PrestaShop.
 *
 * @author    profilweb. <manu@profil-web.fr>
 * @copyright 2021 profil Web.
 * @link      https://github.com/profilweb/pw_homecategories The module's homepage
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<!-- MODULE pw_homeproductcategory -->
<section class="pw-homeproductcategory" id="pw-homeproductcategory" style="--homeproductbg: {$bgcolor}">
    <h4>{l s='Our Offers' mod='pw_homeproductcategory'}</h4>
    <div class="products">
        {foreach from=$products item="product"}
            {include file="catalog/_partials/miniatures/product.tpl" product=$product}
        {/foreach}
    </div>
    <a href="{$allProductsLink}">{l s='Get all offers' mod='pw_homeproductcategory'}</a>
</section>
<!-- /MODULE pw_homecategories -->