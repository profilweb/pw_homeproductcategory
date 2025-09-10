{**
 * Products from one category on HomePage: module for PrestaShop.
 *
 * @author    profilweb. <manu@profil-web.fr>
 * @copyright 2021 profil Web.
 * @link      https://github.com/profilweb/pw_homecategories The module's homepage
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

{if isset($products)}
    <!-- MODULE pw_homeproductcategory -->
    <section class="homeproductcategory" id="pw-homeproductcategory" style="--homeproductbg: {$bgcolor}">
        <h4 class="h4">{l s='Our Offers' mod='pw_homeproductcategory'} {$cat_info['name']}</h4>
        {if !empty($cat_info['img_cat'])}
            <img src="{$cat_info['img_cat']}" alt="">
        {/if}
        <div class="carrou-products">
            <div class="products cycle-slideshow"
                data-cycle-fx="scrollHorz"
                data-cycle-timeout="0"
                data-cycle-slides="> .product"
                data-cycle-prev="#carrou-prev"
                data-cycle-next="#carrou-next"
            >
                {foreach from=$products item="product"}
                    {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                {/foreach}
            </div>
            <div id="carrou-prev" class="nav-cycle cycle-prev"><i class="icon-arrow-left"></i></div>
            <div id="carrou-next" class="nav-cycle cycle-next"><i class="icon-arrow-right"></i></div>
        </div><!-- .carrou-products -->
        <a class="btn btn-secondary" href="{$allProductsLink}">{l s='Get all offers' mod='pw_homeproductcategory'}</a>
    </section>
    <!-- /MODULE pw_homecategories -->
{/if}