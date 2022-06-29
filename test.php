{{ 'component-cart.css' | asset_url | stylesheet_tag }}
{{ 'component-cart-items.css' | asset_url | stylesheet_tag }}
{{ 'component-totals.css' | asset_url | stylesheet_tag }}
{{ 'component-price.css' | asset_url | stylesheet_tag }}
{{ 'component-discounts.css' | asset_url | stylesheet_tag }}
{{ 'component-loading-overlay.css' | asset_url | stylesheet_tag }}

{%- style -%}
.section-{{ section.id }}-padding {
padding-top: {{ section.settings.padding_top | times: 0.75 | round: 0 }}px;
padding-bottom: {{ section.settings.padding_bottom | times: 0.75 | round: 0 }}px;
}

@media screen and (min-width: 750px) {
.section-{{ section.id }}-padding {
padding-top: {{ section.settings.padding_top }}px;
padding-bottom: {{ section.settings.padding_bottom }}px;
}
}
{%- endstyle -%}

<script src="{{ 'cart.js' | asset_url }}" defer="defer"></script>

<cart-items style="background: #fff" class="msy-hide page-width{% if cart == empty %} is-empty{% else %} section-{{ section.id }}-padding{% endif %}">
  <div class="title-wrapper-with-link">
    <h1 class="title title--primary">{{ 'sections.cart.title' | t }}</h1>
    <a href="{{ routes.all_products_collection_url }}" class="underlined-link">{{ 'general.continue_shopping' | t }}</a>
  </div>

  <div class="cart__warnings">
    <h1 class="cart__empty-text">{{ 'sections.cart.empty' | t }}</h1>
    <a href="{{ routes.all_products_collection_url }}" class="button">
      {{ 'general.continue_shopping' | t }}
    </a>

    {%- if shop.customer_accounts_enabled and customer == nil -%}
    <h2 class="cart__login-title">{{ 'sections.cart.login.title' | t }}</h2>
    <p class="cart__login-paragraph">
      {{ 'sections.cart.login.paragraph_html' | t: link: routes.account_login_url }}
    </p>
    {%- endif -%}
  </div>
  {{ routes.cart_url | escape }}2222
  <form action="{{ routes.cart_url }}" class="cart__contents critical-hidden" method="post">
    <div class="cart__items">
      <div class="">
        {%- if cart != empty -%}
        <table class="cart-items">
          <thead>
            <tr>
              <th class="caption-with-letter-spacing" colspan="2" scope="col">{{ 'sections.cart.headings.product' | t }}</th>
              <th class="medium-hide large-up-hide right caption-with-letter-spacing" colspan="1" scope="col">{{ 'sections.cart.headings.total' | t }}</th>
              <th class="cart-items__heading--wide small-hide caption-with-letter-spacing" colspan="1" scope="col">{{ 'sections.cart.headings.quantity' | t }}</th>
              <th class="small-hide right caption-with-letter-spacing" colspan="1" scope="col">{{ 'sections.cart.headings.total' | t }}</th>
            </tr>
          </thead>

          <tbody>
            {%- for item in cart.items -%}
            <tr class="cart-item" id="CartItems-{{ item.index | plus: 1 }}">
              <td class="cart-item__media">
                {% if item.image %}
                {% comment %} Leave empty space due to a:empty CSS display: none rule {% endcomment %}
                <a href="{{ item.url }}" class="cart-item__link" aria-hidden="true" tabindex="-1"> </a>
                <div class="cart-item__image-container gradient global-media-settings">
                  <img src="{{ item.image | img_url: '300x' }}" class="cart-item__image" alt="{{ item.image.alt | escape }}" loading="lazy" width="150" height="{{ 150 | divided_by: item.image.aspect_ratio | ceil }}">
                </div>
                {% endif %}
              </td>

              <td class="cart-item__details">
                {%- if section.settings.show_vendor -%}
                <p class="caption-with-letter-spacing light">{{ item.product.vendor }}</p>
                {%- endif -%}

                <a href="{{ item.url }}" class="cart-item__name h4 break">{{ item.product.title | escape }}</a>

                {%- if item.original_price != item.final_price -%}
                <div class="cart-item__discounted-prices">
                  <span class="visually-hidden">
                    {{ 'products.product.price.regular_price' | t }}
                  </span>
                  <s class="cart-item__old-price product-option">
                    {{- item.original_price | money -}}
                  </s>
                  <span class="visually-hidden">
                    {{ 'products.product.price.sale_price' | t }}
                  </span>
                  <strong class="cart-item__final-price product-option">
                    {{ item.final_price | money }}
                  </strong>
                </div>
                {%- else -%}
                <div class="product-option">
                  {{ item.original_price | money }}
                </div>
                {%- endif -%}

                {%- if item.product.has_only_default_variant == false or item.properties.size != 0 or item.selling_plan_allocation != nil -%}
                <dl>
                  {%- if item.product.has_only_default_variant == false -%}
                  {%- for option in item.options_with_values -%}
                  <div class="product-option">
                    <dt>{{ option.name }}: </dt>
                    <dd>{{ option.value }}</dd>
                  </div>
                  {%- endfor -%}
                  {%- endif -%}

                  {%- for property in item.properties -%}
                  {%- assign property_first_char = property.first | slice: 0 -%}
                  {%- if property.last != blank and property_first_char != '_' -%}
                  <div class="product-option">
                    <dt>{{ property.first }}: </dt>
                    <dd>
                      {%- if property.last contains '/uploads/' -%}
                      <a href="{{ property.last }}" class="link" target="_blank">
                        {{ property.last | split: '/' | last }}
                      </a>
                      {%- else -%}
                      {{ property.last }}
                      {%- endif -%}
                    </dd>
                  </div>
                  {%- endif -%}
                  {%- endfor -%}
                </dl>

                <p class="product-option">{{ item.selling_plan_allocation.selling_plan.name }}</p>
                {%- endif -%}

                <ul class="discounts list-unstyled" role="list" aria-label="{{ 'customer.order.discount' | t }}">
                  {%- for discount in item.discounts -%}
                  <li class="discounts__discount">
                    {%- render 'icon-discount' -%}
                    {{ discount.title }}
                  </li>
                  {%- endfor -%}
                </ul>
              </td>

              <td class="cart-item__totals right medium-hide large-up-hide">
                <div class="loading-overlay hidden">
                  <div class="loading-overlay__spinner">
                    <svg aria-hidden="true" focusable="false" role="presentation" class="spinner" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                      <circle class="path" fill="none" stroke-width="6" cx="33" cy="33" r="30"></circle>
                    </svg>
                  </div>
                </div>
                <div class="cart-item__price-wrapper">
                  {%- if item.original_line_price != item.final_line_price -%}
                  <dl class="cart-item__discounted-prices">
                    <dt class="visually-hidden">
                      {{ 'products.product.price.regular_price' | t }}
                    </dt>
                    <dd>
                      <s class="cart-item__old-price price price--end">
                        {{ item.original_line_price | money }}
                      </s>
                    </dd>
                    <dt class="visually-hidden">
                      {{ 'products.product.price.sale_price' | t }}
                    </dt>
                    <dd class="price price--end">
                      {{ item.final_line_price | money }}
                    </dd>
                  </dl>
                  {%- else -%}
                  <span class="price price--end">
                    {{ item.original_line_price | money }}
                  </span>
                  {%- endif -%}

                  {%- if item.variant.available and item.unit_price_measurement -%}
                  <div class="unit-price caption">
                    <span class="visually-hidden">{{ 'products.product.price.unit_price' | t }}</span>
                    {{ item.variant.unit_price | money }}
                    <span aria-hidden="true">/</span>
                    <span class="visually-hidden">&nbsp;{{ 'accessibility.unit_price_separator' | t }}&nbsp;</span>
                    {%- if item.variant.unit_price_measurement.reference_value != 1 -%}
                    {{- item.variant.unit_price_measurement.reference_value -}}
                    {%- endif -%}
                    {{ item.variant.unit_price_measurement.reference_unit }}
                  </div>
                  {%- endif -%}
                </div>
              </td>

              <td class="cart-item__quantity">
                <div class="cart-item__quantity-wrapper">
                  <label class="visually-hidden" for="Quantity-{{ item.index | plus: 1 }}">
                    {{ 'products.product.quantity.label' | t }}
                  </label>
                  <quantity-input class="quantity">
                    <button class="quantity__button no-js-hidden" name="minus" type="button">
                      <span class="visually-hidden">{{ 'products.product.quantity.decrease' | t: product: item.product.title | escape }}</span>
                      {% render 'icon-minus' %}
                    </button>
                    <input class="quantity__input" type="number" name="updates[]" value="{{ item.quantity }}" min="0" aria-label="{{ 'products.product.quantity.input_label' | t: product: item.product.title | escape }}" data-index="{{ item.index | plus: 1 }}">
                    <button class="quantity__button no-js-hidden" name="plus" type="button">
                      <span class="visually-hidden">{{ 'products.product.quantity.increase' | t: product: item.product.title | escape }}</span>
                      {% render 'icon-plus' %}
                    </button>
                  </quantity-input>

                  <cart-remove-button id="Remove-{{ item.index | plus: 1 }}" data-index="{{ item.index | plus: 1 }}">
                    <a href="{{ item.url_to_remove }}" class="button button--tertiary" aria-label="{{ 'sections.cart.remove_title' | t: title: item.title }}">
                      {% render 'icon-remove' %}
                    </a>
                  </cart-remove-button>
                </div>
                <div class="cart-item__error" id="Line-item-error-{{ item.index | plus: 1 }}" role="alert">
                  <small class="cart-item__error-text"></small>
                  <svg aria-hidden="true" focusable="false" role="presentation" class="icon icon-error" viewBox="0 0 13 13">
                    <circle cx="6.5" cy="6.50049" r="5.5" stroke="white" stroke-width="2" />
                    <circle cx="6.5" cy="6.5" r="5.5" fill="#EB001B" stroke="#EB001B" stroke-width="0.7" />
                    <path d="M5.87413 3.52832L5.97439 7.57216H7.02713L7.12739 3.52832H5.87413ZM6.50076 9.66091C6.88091 9.66091 7.18169 9.37267 7.18169 9.00504C7.18169 8.63742 6.88091 8.34917 6.50076 8.34917C6.12061 8.34917 5.81982 8.63742 5.81982 9.00504C5.81982 9.37267 6.12061 9.66091 6.50076 9.66091Z" fill="white" />
                    <path d="M5.87413 3.17832H5.51535L5.52424 3.537L5.6245 7.58083L5.63296 7.92216H5.97439H7.02713H7.36856L7.37702 7.58083L7.47728 3.537L7.48617 3.17832H7.12739H5.87413ZM6.50076 10.0109C7.06121 10.0109 7.5317 9.57872 7.5317 9.00504C7.5317 8.43137 7.06121 7.99918 6.50076 7.99918C5.94031 7.99918 5.46982 8.43137 5.46982 9.00504C5.46982 9.57872 5.94031 10.0109 6.50076 10.0109Z" fill="white" stroke="#EB001B" stroke-width="0.7">
                  </svg>
                </div>
              </td>

              <td class="cart-item__totals right small-hide">
                <div class="loading-overlay hidden">
                  <div class="loading-overlay__spinner">
                    <svg aria-hidden="true" focusable="false" role="presentation" class="spinner" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                      <circle class="path" fill="none" stroke-width="6" cx="33" cy="33" r="30"></circle>
                    </svg>
                  </div>
                </div>

                <div class="cart-item__price-wrapper">
                  {%- if item.original_line_price != item.final_line_price -%}
                  <dl class="cart-item__discounted-prices">
                    <dt class="visually-hidden">
                      {{ 'products.product.price.regular_price' | t }}
                    </dt>
                    <dd>
                      <s class="cart-item__old-price price price--end">
                        {{ item.original_line_price | money }}
                      </s>
                    </dd>
                    <dt class="visually-hidden">
                      {{ 'products.product.price.sale_price' | t }}
                    </dt>
                    <dd class="price price--end">
                      {{ item.final_line_price | money }}
                    </dd>
                  </dl>
                  {%- else -%}
                  <span class="price price--end">
                    {{ item.original_line_price | money }}
                  </span>
                  {%- endif -%}

                  {%- if item.variant.available and item.unit_price_measurement -%}
                  <div class="unit-price caption">
                    <span class="visually-hidden">{{ 'products.product.price.unit_price' | t }}</span>
                    {{ item.variant.unit_price | money }}
                    <span aria-hidden="true">/</span>
                    <span class="visually-hidden">&nbsp;{{ 'accessibility.unit_price_separator' | t }}&nbsp;</span>
                    {%- if item.variant.unit_price_measurement.reference_value != 1 -%}
                    {{- item.variant.unit_price_measurement.reference_value -}}
                    {%- endif -%}
                    {{ item.variant.unit_price_measurement.reference_unit }}
                  </div>
                  {%- endif -%}
                </div>
              </td>
            </tr>
            {%- endfor -%}
          </tbody>
        </table>
        {%- endif -%}
      </div>
    </div>

    <p class="visually-hidden" id="cart-live-region-text" aria-live="polite" role="status"></p>
    <p class="visually-hidden" id="shopping-cart-line-item-status" aria-live="polite" aria-hidden="true" role="status">{{ 'accessibility.loading' | t }}</p>
  </form>
</cart-items>

<cart-items>
  <div class="cart-items-wrapper">
    <!-- bbreadcrumb -->
    <ul class="breadcrumb flex">
      <li class="flex">Top</li>
      <li>
        <a href="#">購入画面</a>
      </li>
    </ul>
    <!-- shopping-cart -->
    <section class="shopping-cart">
      <h1 class="title text-center">ショッピングカート</h1>
      <hr class="border-min">
      </hr>
      <div class="flex content">
        <p class="text-center">
          ダミーEコマースなどのゲームコンテンツ・サービスの提供を通じて、ダ
          ミーEコマースなどのゲームコンテンツ・サービスの提供を通じて、ダ
          ミーお客様に常に最高のエンタテインメント機器をダミーお届けするよう努めてまいります。
        </p>
      </div>
      <!-- border full line -->
      <hr class="border-full">
      </hr>
    </section>

    <!-- rental-period -->
    <section class="rental-period">
      <h1 class="title-first text-center">レンタル期間</h1>
      <!-- border min line -->
      <hr class="border-min">
      </hr>
      <!-- border min line -->
      <h1 class="title-child text-center">お届け日</h1>
      <div class="date-main">
        <div class="date-child flex justify-content-center">
          <svg width="61" height="49">
            <image xlink:href="https://cdn.shopify.com/s/files/1/0611/5082/2597/files/icon-date.svg?v=1656400627" src="https://cdn.shopify.com/s/files/1/0611/5082/2597/files/icon-date.svg?v=1656400627" width="61" height="49"></image>
          </svg>
          <span>5/29</span>
        </div>
      </div>
      <!-- note shopping-cart -->
      <h2 class="note text-center">14日以降なら、いつでも返却可能</h2>
    </section>
    <form action="{{ routes.cart_url }}" class="cart__contents critical-hidden" method="post" id="cart">
      {%- for item in cart.items -%}
      <!-- shopping-cart-body -->
      <section class="shopping-cart-body flex flex-col align-items-center" id="CartItem-{{ item.index | plus: 1 }}">
        <svg width="670" height="400">
          <a href="{{ item.url | default: '#' }}">
            <image xlink:href="{{ item.image | img_url: '533x' }}" src="{{ item.image | img_url: '533x' }}" width="670" height="400">
            </image </a>
        </svg>

        <div class="body-shop">
          <h1 class="title text-center">
            {{ item.title }}
          </h1>
          <!-- detail 1 -->
          <div class="detail flex justify-content-center">
            <span class="price-number">{{ item.price | escape }}</span>
            <span class="type-price">円</span>
            <span class="month">/ 月額</span>
            <span class="item-quantity" />✕ &nbsp;<span id="Quantitys-{{ item.index | plus: 1 }}">{{ item.quantity | escape }}</span> 点</span>
          </div>
          <!-- detail 2 -->
          <div class="detail2 flex justify-content-center">
            <quantity-input class="quantity cart-plus-minus rounded flex align-items-center">
              <button class="quantity__button no-js-hidden dec qtybutton" name="minus" type="button">
                <span class="visually-hidden">{{ 'products.product.quantity.decrease' | t: product: item.product.title | escape }}</span>
                {% render 'icon-minus' %}
              </button>
              <input class="quantity__input btn-shadow rounded dropcart-plus-minus-box" type="number" name="updates[]" value="{{ item.quantity }}" min="0" aria-label="{{ 'products.product.quantity.input_label' | t: product: item.product.title | escape }}" id="Quantity-{{ item.index | plus: 1 }}" data-index="{{ item.index | plus: 1 }}">
              <button class="quantity__button no-js-hidden inc qtybutton" name="plus" type="button">
                <span class="visually-hidden">{{ 'products.product.quantity.increase' | t: product: item.product.title | escape }}</span>
                {% render 'icon-plus' %}
              </button>
            </quantity-input>
            <a href="{{ item.url_to_remove }}" class="btn btn-shadow rounded flex align-items-center">
              <svg width="50" height="62">
                <image xlink:href="https://cdn.shopify.com/s/files/1/0611/5082/2597/files/icon-trash.svg?v=1656400627" width="50" height="62"></image>
              </svg>
              削除
            </a>
          </div>

        </div>
      </section>
      <!-- border full line -->
      <hr class="border-full">
      </hr>
      {% endfor %}

      <!-- detail-footer -->
      <div class="detail-footer flex justify-content-center">
        <a href="/cart/clear" class="btn btn-secondary rounded-full flex align-items-center">
          <svg width="50" height="62">
            <image xlink:href="https://cdn.shopify.com/s/files/1/0611/5082/2597/files/icon-trash.svg?v=1656400627" src="https://cdn.shopify.com/s/files/1/0611/5082/2597/files/icon-trash.svg?v=1656400627" width="50" height="62"></image>
          </svg>
          カートを空にする
        </a>
      </div>

      <!-- minimum-rental-period -->
      <section class="minimum-rental-period" id="main-cart-items" data-id="{{ section.id }}">
        <div id="js-contents">
          <h2 class="title text-center">最低レンタル期間は14日です</h2>
          <p>
            この規約（以下「本規約」といいます。）は、レンティオ株式会社（以下「当社」といいます。）が提供するサービス「Rentio」（以下「本サービス」といいます。）の利用に関する条件を定めるものであり、本サービスを利用するすべてのお客様（以下「ユーザー」といいます。）に適用されます。
          </p>
          <div class="total-order text-center">
            <h2 class="total-order-heading">注文合計・税込</h2>
            <!-- border min line -->
            <hr class="border-min">
            </hr>
            <div class="detail-total-order flex justify-content-center">
              <span id="" class="price-number">{{ cart.total_price }}</span>
              <span class="type-price">円</span>
              <span class="tax">往復送料込み</span>
            </div>
          </div>
          <div class="button-order text-center">
            <!--         <input type="submit" id="checkout" class="btn btn-success rounded-full" value="¥ レジに進む"> -->
            <button type="submit" id="checkout" class="btn btn-success rounded-full" name="checkout" {% if cart == empty %} disabled{% endif %} form="cart">
              ¥ レジに進む
            </button>
          </div>
        </div>
      </section>


    </form>

  </div>
</cart-items>




<style>
  .msy-hide {
    display: none;
  }

  body {
    color: #fff;
  }

  * {
    margin: 0;
  }

  a {
    text-decoration: none;
    color: #fff;
  }

  .cart-items-wrapper {
    background-color: #000;
    color: #fff;
  }

  .flex {
    display: flex;
  }

  .flex-col {
    flex-direction: column;
  }

  .quantity__input:focus {
    background-color: black;
    outline: none;
    border: none;
  }

  .justify-content-center {
    justify-content: center;
  }

  .align-items-center {
    align-items: center;
  }

  .text-center {
    text-align: center;
  }

  /* Border line */
  .border-min {
    position: absolute;
    left: 0;
    right: 0;
    border-bottom: 1px solid #fff;
    width: 60px;
    margin: 0 auto;
  }

  .border-full {
    border-bottom: 1px solid #fff;
    width: 100%;
    margin: 0;
  }

  .rounded {
    border-radius: 0.25rem;
  }

  .rounded-full {
    border-radius: 9999px;
  }

  /* btn all */
  .dropdown-toggle {
    font-size: 38px !important;
  }

  .btn-shadow {
    padding: 10px 37px;
    width: 45%;
  }

  .dropdown-toggle {
    padding: 18px 60px 18px 25px;
  }

  .btn {
    background-color: transparent;
    color: #fff;
    font-size: 33px;
    font-family: "Bebas Neue", cursive;
    cursor: pointer;
    border: 1px solid #fff;
  }

  .btn-secondary {
    padding: 15px 101px;
    background-color: #2e2e2e;
  }

  .btn-success {
    padding: 27px 170px;
    background-color: #00b94e;
  }

  .btn svg,
  .btn-shadow svg {
    margin-right: 14px;
  }

  /* breadcrumb */
  .breadcrumb li {
    align-items: flex-end;
  }

  .breadcrumb li:first-child {
    color: #656565;
    font-weight: bold;
    font-size: 18px;
  }

  .breadcrumb li {
    list-style: none;
    text-transform: uppercase;
  }

  .breadcrumb li a {
    position: relative;
  }

  .breadcrumb li a::after {
    content: "";
    display: block;
    position: absolute;
    left: 0;
    right: 0;
    border-bottom: 1px solid #fff;
    font-size: 18px;
  }

  .breadcrumb>li+li::before {
    content: ">";
    color: #656565;
    margin: 0 13px 0 5px;
    font-weight: bold;
    font-size: 18px;
  }

  /* shopping-cart */
  .shopping-cart h1 {
    font-family: "Bebas Neue", cursive;
    font-size: 50px;
    font-weight: bold;
    margin: 84px 0px 28px 0;
  }

  .shopping-cart .content p {
    height: 50px;
    overflow: hidden;
    font-size: 16px;
    margin: 38px 110px 58px 110px;
  }

  /* rental-period */
  .rental-period .title-first {
    font-family: "Bebas Neue", cursive;
    font-size: 33px;
    font-weight: bold;
    margin: 51px 275px 28px 275px;
    color: #fff;
  }

  .rental-period .title-child {
    font-family: "Bebas Neue", cursive;
    font-size: 25px;
    font-weight: bold;
    padding: 26px 0px 30px 0px;
  }

  .rental-period .date-main {
    background-color: #696969;
    height: 100px;
  }

  .rental-period .date-main span {
    font-family: "Bebas Neue", cursive;
    font-size: 45px;
  }

  .rental-period .date-main svg {
    margin-right: 23px;
  }

  .rental-period .date-main .date-child {
    padding: 26px 0px 21px 0px;
  }

  .rental-period .note {
    font-family: "Bebas Neue", cursive;
    font-size: 25px;
    text-decoration: underline;
    margin: 31px 0px 30px 0px;
  }

  /* shopping-cart-body */
  .shopping-cart-body {
    margin: 0px 40px 36px 40px;
  }

  .shopping-cart-body .body-shop .title {
    height: 50px;
    overflow: hidden;
    font-family: "Bebas Neue", cursive;
    font-size: 33px;
    font-weight: bold;
    margin: 35px 0px 24px 0px;
    color: #fff;
  }

  .shopping-cart-body .body-shop .price-number {
    font-size: 88px;
    font-weight: bold;
    font-family: "Bebas Neue", cursive;
    color: #fff;
  }

  .shopping-cart-body .body-shop .type-price {
    margin: 0px 15px 0px 16px;
  }

  .shopping-cart-body .body-shop .month {
    margin-right: 39px;
  }

  .shopping-cart-body .body-shop .detail {
    align-items: baseline;
    font-size: 38px;
    font-weight: bold;
    font-family: "Bebas Neue", cursive;
  }

  .shopping-cart-body .body-shop .detail2 {
    gap: 114px;
    margin: 36px 0px 62px 0px;
    display: flex;
    align-items: stretch;
  }

  select {
    width: 200px;
    background: url("https://cdn.shopify.com/s/files/1/0611/5082/2597/files/icon-arrow-down.svg?v=1656400627") no-repeat right #ddd;
    -webkit-appearance: none;
    background-position-y: 25px;
    background-position-x: 150px;
  }

  .shopping-cart-body .body-shop .detail-footer {
    margin: 61px 0px 70px 0px;
  }

  /* minimum-rental-period */
  .minimum-rental-period .title {
    padding: 44px 0px 20px 0px;
    font-size: 25px;
    font-family: "Bebas Neue", cursive;
    text-decoration: underline;
  }

  .minimum-rental-period .total-order .total-order-heading {
    padding: 21px 0px 26px 0px;
    font-size: 30px;
    font-family: "Bebas Neue", cursive;
    color: #fff;
  }

  .minimum-rental-period p {
    margin: 0px 40px;
    font-size: 16px;
    font-family: "Bebas Neue", cursive;
  }

  .minimum-rental-period .total-order {
    width: 100%;
    height: 300px;
    background-color: #696969;
    margin: 62px 0px 52px 0px;
  }

  .minimum-rental-period .button-order {
    margin: 70px 0px;
  }

  .minimum-rental-period .total-order .detail-total-order {
    align-items: baseline;
    flex-wrap: wrap;
    line-height: normal;
  }

  .minimum-rental-period .total-order .detail-total-order .price-number {
    font-size: 133px;
    margin-right: 17px;
    font-family: "Bebas Neue", cursive;
    color: #fff;
  }

  .minimum-rental-period .total-order .detail-total-order .type-price {
    display: block;
    font-size: 58px;
    font-family: "Bebas Neue", cursive;
  }

  .minimum-rental-period .total-order .detail-total-order .tax {
    flex: 100% 0 0;
    font-size: 18px;
    font-family: "Bebas Neue", cursive;
  }

  .question-order {
    margin: 70px 110px;
    font-family: "Bebas Neue", cursive;
  }

  .question-order .question-title,
  .question-order .question-content p {
    margin-bottom: 40px;
  }

  .question-order .question-content .question-content-title {
    margin-bottom: 15px;
  }

  /* Quantity input */
  .cart-plus-minus {
    border: 1px solid #fff;
    color: #fff;
    width: 45%;
  }

  .cart-plus-minus .dropcart-plus-minus-box {
    height: 70px;
    width: 40px;
    border-style: none;
    text-align: center;
    font-size: 1.4rem;
    font-weight: 500;
    opacity: 0.85;
    outline: none;
    padding: 0 0.5rem;
  }

  .cart-plus-minus .qtybutton {
    width: calc(4.5rem / 1);
    height: 100px;
    display: flex;
    flex: auto 0 0;
    justify-content: center;
    align-items: center;
    font-size: 1.8rem;
    border: 0;
    color: inherit;
    background-color: transparent;
    cursor: pointer;
  }

  .shopping-cart-body .item-divider {
    background: url(../images/bg_divider.png) repeat-x 7px;
    width: 100%;
    filter: saturate(100%) contrast(100%) brightness(100%) invert(100%) sepia(100%) grayscale(100%);
    height: 1px;
    margin-bottom: 60px;
    border: none;
  }

  .detail-footer.justify-content-center {
    margin-top: 50px;
  }

  @media (min-width: 320px) and (max-width: 749px) {
    .row {
      margin: 0px 15px;
    }

    .border-full,
    .rental-period .date-main,
    .minimum-rental-period .total-order {
      margin: 62px -15px;
      padding: 0 15px;
    }

    .btn {
      font-size: 29px;
    }

    .minimum-rental-period .button-order {
      margin-bottom: 40px;
    }

    .minimum-rental-period .title {
      font-size: 22px;
      padding-top: 0;
    }

    .shopping-cart h1 {
      font-size: 32px;
    }

    .rental-period .title-child {
      padding-bottom: 0;
    }

    .shopping-cart .content p {
      margin-bottom: -50px;
    }
  }

  @media (min-width: 320px) and (max-width: 580px) {
    .minimum-rental-period .total-order .detail-total-order .price {
      font-size: 80px;
    }

    .minimum-rental-period .total-order .detail-total-order {
      flex-direction: column;
      align-items: center;
    }
  }

  @media (min-width: 320px) and (max-width: 556px) {
    .shopping-cart-body .body-shop .detail2 {
      flex-direction: column;
      gap: 20px;
      align-items: center;
    }

    .cart-plus-minus .dropcart-plus-minus-box {
      height: 100px;
    }
  }

  @media (min-width: 320px) and (max-width: 493px) {
    .shopping-cart-body .body-shop .detail {
      flex-wrap: wrap;
    }
  }

  @media (min-width: 320px) and (max-width: 339px) {
    .btn-secondary {
      font-size: 22px;
    }
  }

  @media (min-width: 320px) and (max-width: 398px) {
    .rental-period .note {
      font-size: 19px;
    }
  }
</style>



{% schema %}
{
"name": "t:sections.main-cart-items.name",
"settings": [
{
"type": "checkbox",
"id": "show_vendor",
"default": false,
"label": "t:sections.main-cart-items.settings.show_vendor.label"
},
{
"type": "header",
"content": "t:sections.all.padding.section_padding_heading"
},
{
"type": "range",
"id": "padding_top",
"min": 0,
"max": 100,
"step": 4,
"unit": "px",
"label": "t:sections.all.padding.padding_top",
"default": 36
},
{
"type": "range",
"id": "padding_bottom",
"min": 0,
"max": 100,
"step": 4,
"unit": "px",
"label": "t:sections.all.padding.padding_bottom",
"default": 36
}
]
}
{% endschema %}