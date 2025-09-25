@extends('layouts.mini')

@section('title','Mahsulot')

@section('content')
<div class="page">
  <div class="card mini-card p-3 mb-3">
    <div id="images" class="mb-3 text-center text-secondary">Rasm topilmadi</div>
    <div id="head"></div>
  </div>

  <div id="attrs" class="card mini-card p-3 mb-3 d-none">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0">{{ __('messages.attributes') }}</h6>
      <button id="attrsToggle" type="button" class="btn btn-sm btn-outline-primary">{{ __('messages.show') ?? "Ko'rsatish" }}</button>
    </div>
    <div id="attrsBody" class="d-none"></div>
  </div>

  <div id="variants" class="card mini-card p-3 mb-3 d-none">
    <h6 class="mb-2">{{ __('messages.variants') }}</h6>
    <div id="variantsBody"></div>
  </div>

  <!-- @if(!empty($offerId))
  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-2">Xitoy ichki yetkazib berish (freight)ni tekshirish</h6>
    <form method="POST" action="{{ route('mini.freight') }}" class="row g-2">
      @csrf
      <input type="hidden" name="offerId" value="{{ $offerId }}">
      <div class="col-4"><input class="form-control mini-input" name="toProvinceCode" placeholder="330000"></div>
      <div class="col-4"><input class="form-control mini-input" name="toCityCode" placeholder="330100"></div>
      <div class="col-4"><input class="form-control mini-input" name="toCountryCode" placeholder="330108"></div>
      <div class="col-12"><input class="form-control mini-input" name="totalNum" type="number" min="1" value="1"></div>
      <div class="col-12"><button class="btn btn-mini w-100" type="submit"><i class="bi bi-truck"></i> Freight hisoblash</button></div>
    </form>
  </div>
  @endif -->

      <div class="d-grid gap-2">
        <button class="btn btn-mini" onclick="addToCart()"><i class="bi bi-cart-plus"></i> {{ __('messages.add_to_cart') }}</button>
        <button class="btn btn-mini" onclick="checkout()"><i class="bi bi-lightning-charge"></i> {{ __('messages.checkout') }}</button>
      </div>
</div>

<script>
  const productRaw = @json($product);
  const product = (productRaw && productRaw.data) ? productRaw.data : productRaw;
  const imagesEl = document.getElementById('images');
  const headEl = document.getElementById('head');
  const attrsWrap = document.getElementById('attrs');
  const attrsBody = document.getElementById('attrsBody');
  const variantsWrap = document.getElementById('variants');
  const variantsBody = document.getElementById('variantsBody');
  const style = document.createElement('style');
  style.textContent = `.chip-select{cursor:pointer}.chip-select.active{background:#1d72f2;color:#fff}`;
  document.head.appendChild(style);

  function pick(...keys){
    for (const k of keys){ if (k && k in product && product[k]) return product[k]; }
    return null;
  }

  if (!product || (typeof product === 'object' && Object.keys(product).length === 0)) {
    headEl.innerHTML = '<div class="text-secondary">Ma\'lumot topilmadi (product bo\'sh). </div>';
    const pre = document.createElement('pre');
    pre.style.whiteSpace = 'pre-wrap';
    pre.textContent = JSON.stringify(productRaw, null, 2);
    imagesEl.replaceWith(pre);
    console.warn('Empty product payload:', productRaw);
  }

  const title = (product && (product.title || product.subject || product.name)) || 'Mahsulot';
  const shop = product.shopName || product.seller || product.companyName || '-';
  // Prefer range first (1688) to avoid large formatted numbers in product.price
  let price = null;
  const saleInfo = product.productSaleInfo || {};
  const priceRangeCandidates = product.priceRange || product.priceRanges || saleInfo.priceRangeList || saleInfo.priceRanges;
  if (Array.isArray(priceRangeCandidates) && priceRangeCandidates.length){
    price = priceRangeCandidates[0].price ?? priceRangeCandidates[0].value ?? null;
  }
  if (!price) price = product.minPrice || product.discountPrice || product.referencePrice || product.promotionPrice || product.price;
  const rate = {{ (float) optional(\App\Models\AdminSetting::first())->cny_to_uzs ?? 0 }};
  const currencySuffix = rate > 0 ? " so'm" : ' yuan';
  const sale = product.productSaleInfo || {};
  const pr = product.priceRange || product.priceRanges || sale.priceRanges || sale.priceRangeList;
  if (!price && Array.isArray(pr) && pr.length){
    const first = pr[0].price ?? pr[0].value; const last = pr.at(-1).price ?? pr.at(-1).value; price = first && last ? `${first} ~ ${last}` : (first ?? last);
  }
  function toNumber(v){
    if (v === undefined || v === null) return null;
    if (typeof v === 'number') return v;
    const n = parseFloat(String(v).replace(/[^\d.]/g,'').replace(/\.(?=.*\.)/g,''));
    return isNaN(n) ? null : n;
  }
  // If price still empty, try ranges
  if (!price){
    const sale = product.productSaleInfo || {};
    const pr = product.priceRange || product.priceRanges || sale.priceRanges || sale.priceRangeList;
    if (Array.isArray(pr) && pr.length){
      const first = pr[0].price ?? pr[0].value;
      price = first ?? null;
    }
  }
  const numPrice = toNumber(price);
  const displayPrice = (numPrice !== null) ? (rate > 0 ? Math.round(numPrice * rate) : numPrice) : '-';
  headEl.innerHTML = `<div class=\"fw-semibold mb-1\">${title}</div>`+
                     `<div class=\"mb-1\">${__('messages.price') ?? 'Narx'}: <span class=\"chip\">${displayPrice}${currencySuffix}</span></div>`+
                     `<small class=\"text-secondary d-block\">${__('messages.shop') ?? "Do'kon"}: ${shop}</small>`;

  // Try different image sources for Taobao products (extended)
  let imgs = pick('images') || pick('imageList') || pick('gallery') || 
             (product && product.productImage && (product.productImage.images || product.productImage.imageList)) || 
             pick('productImage') ||
             (product && product.itemImages && product.itemImages.map(img => img.url)) ||
             (product && product.images && product.images.map(img => img.url || img)) ||
             (product && product.small_images && (product.small_images.string || product.small_images)) ||
             (product && product.topImages) ||
             (product && product.detailImages) ||
             (product && product.descImgs) ||
             (product && product.picsPath) ||
             (product && product.imageUrls) ||
             (product && product.mainImage && [product.mainImage]) ||
             (product && product.mainPic && [product.mainPic]) ||
             (product && product.picUrl && [product.picUrl]) ||
             (product && product.image && [product.image]);
  
  if (imgs && imgs.images) imgs = imgs.images;
  if (typeof imgs === 'string') imgs = [imgs];
  if (imgs && imgs.length){
    const slides = imgs.map((it,idx) => {
      const src = typeof it === 'string' ? it : (it.url || it.fullPathImageURI || it.imageUrl || '');
      return `<div class=\"carousel-item ${idx===0?'active':''}\"><img src=\"${src}\" referrerpolicy=\"no-referrer\" crossorigin=\"anonymous\" loading=\"lazy\" class=\"d-block w-100\" style=\"max-height:260px;object-fit:cover\"/></div>`;
    }).join('');
    const indicators = imgs.map((_,i)=>`<button type=\"button\" data-bs-target=\"#prodCarousel\" data-bs-slide-to=\"${i}\" ${i===0?'class=\"active\" aria-current=\"true\"':''} aria-label=\"Slide ${i+1}\"></button>`).join('');
    imagesEl.innerHTML = `<div id=\"prodCarousel\" class=\"carousel slide\" data-bs-ride=\"carousel\">`
      + `<div class=\"carousel-indicators\">${indicators}</div>`
      + `<div class=\"carousel-inner\" style=\"border-radius:12px;overflow:hidden\">${slides}</div>`
      + `<button class=\"carousel-control-prev\" type=\"button\" data-bs-target=\"#prodCarousel\" data-bs-slide=\"prev\">`
      + `<span class=\"carousel-control-prev-icon\" aria-hidden=\"true\"></span><span class=\"visually-hidden\">Previous</span></button>`
      + `<button class=\"carousel-control-next\" type=\"button\" data-bs-target=\"#prodCarousel\" data-bs-slide=\"next\">`
      + `<span class=\"carousel-control-next-icon\" aria-hidden=\"true\"></span><span class=\"visually-hidden\">Next</span></button>`
      + `</div>`;
    imagesEl.classList.remove('text-secondary');
  }

  // Try different attribute sources for Taobao products
  const attrs = product.props || product.attributes || product.skuProps || product.attributeList || product.productAttribute || 
                product.itemProps || product.props || product.skuProps || product.attributes || [];
  if (attrs && Object.keys(attrs).length){
    attrsWrap.classList.remove('d-none');
    const rows = [];
    (Array.isArray(attrs) ? attrs : Object.entries(attrs)).forEach((entry)=>{
      let label, val;
      if (Array.isArray(entry)) { label = entry[0]; val = entry[1]; }
      else { label = entry.name || ''; val = entry.value ?? entry.valueName ?? entry.valueDisplayName ?? entry.propertyValue ?? entry.values; }
      if (Array.isArray(val)) val = val.join(', ');
      rows.push(`<div class="d-flex justify-content-between py-1"><div>${label || ''}</div><div>${val || '-'}</div></div>`);
    });
    attrsBody.innerHTML = rows.join('');
    const btn = document.getElementById('attrsToggle');
    btn.addEventListener('click', ()=>{
      const hidden = attrsBody.classList.toggle('d-none');
      btn.textContent = hidden ? (window.__t_hide || "{{ __('messages.show') }}") : (window.__t_show || "{{ __('messages.hide') }}");
    });
  }

  // Build variants from skuProps or derive from productSkuInfos (Taobao support)
  let skuProps = product.skuProps || product.skuAttributes || product.skuPropertyList || [];
  // Taobao specific structure: skuBase.props
  if ((!skuProps || !skuProps.length) && product.skuBase && Array.isArray(product.skuBase.props)) {
    skuProps = product.skuBase.props.map(p => ({
      name: p.name || p.prop || 'Option',
      values: (p.values || p.items || []).map(v => (typeof v === 'string') ? v : (v.name || v.value || v.valueName || v.valueDisplayName)).filter(Boolean)
    }));
  }

  // Global selections object (used by addToCart/checkout)
  let selections = {};
  if ((!skuProps || !skuProps.length) && Array.isArray(product.productSkuInfos)) {
    const byName = {};
    product.productSkuInfos.forEach(sku => {
      (sku.skuAttributes || []).forEach(a => {
        const key = a.attributeNameTrans || a.attributeName || 'Option';
        byName[key] = byName[key] || new Set();
        byName[key].add(a.valueTrans || a.value);
      });
    });
    skuProps = Object.entries(byName).map(([name, set]) => ({ name, values: Array.from(set) }));
  }

  console.debug('Product debug:', product);
  console.debug('Product keys:', Object.keys(product || {}));
  console.debug('Product structure:', JSON.stringify(product, null, 2));
  if (skuProps && skuProps.length){
    variantsWrap.classList.remove('d-none');
    variantsBody.innerHTML = skuProps.map(p=>{
      const group = (p.name || p.prop || 'Option');
      const values = p.values || p.value || p.items || [];
      const chips = values.map(v=> {
        const label = (typeof v==='string')? v : (v.name || v.value || '-');
        return `<span class="chip chip-select" data-group="${group}" data-value="${label}">${label}</span>`;
      }).join(' ');
      return `<div class="mb-2"><div class="small text-secondary mb-1">${group}</div><div class="d-flex flex-wrap gap-2">${chips}</div></div>`;
    }).join('');
    // selections is defined globally above
    variantsBody.addEventListener('click', (e)=>{
      const el = e.target.closest('.chip-select');
      if (!el) return;
      const group = el.getAttribute('data-group');
      variantsBody.querySelectorAll(`.chip-select[data-group="${group}"]`).forEach(c=>c.classList.remove('active'));
      el.classList.add('active');
      selections[group] = el.getAttribute('data-value');
    });
    
    // Savatdan kelgan mahsulot uchun avtomatik tanlash
    setTimeout(() => {
      skuProps.forEach((p, pIdx) => {
        const group = (p.name || p.prop || 'Option');
        const values = p.values || p.value || p.items || [];
        if (values.length === 1) {
          // Agar faqat bitta variant bo'lsa, uni avtomatik tanlash
          const chip = variantsBody.querySelector(`.chip-select[data-group="${group}"][data-value="${values[0]}"]`);
          if (chip) {
            chip.classList.add('active');
            selections[group] = values[0];
          }
        }
      });
    }, 100);
  }

  // Savatga qo'shish funksiyasi
  function addToCart() {
    // Enforce variant selection if variants exist
    if (skuProps && skuProps.length){
      const requiredGroups = skuProps.map(p => (p.name || p.prop || 'Option'));
      for (const g of requiredGroups){
        if (!selections[g]){
          if (window.showMiniToast) showMiniToast(`Iltimos, variant tanlang: ${g}`, 'warning');
          return;
        }
      }
    }
    const productId = '{{ $offerId }}' || 'unknown';
    const title = product.subject || product.title || 'Mahsulot';
    const basePrice = (typeof numPrice === 'number' && !isNaN(numPrice)) ? numPrice : 0;
    const price = rate > 0 ? Math.round(basePrice * rate) : basePrice;
    const imageUrl = (product.productImage && product.productImage.images && product.productImage.images[0]) || '';
    const quantity = 1; // Default miqdor
    
    // Tanlangan variantlarni olish
    const selectedVariants = {};
    document.querySelectorAll('.chip-select.active').forEach(chip => {
      const group = chip.getAttribute('data-group');
      const value = chip.getAttribute('data-value');
      if (group && value) {
        selectedVariants[group] = value;
      }
    });

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('product_id', productId);
    formData.append('title', title);
    formData.append('price', price);
    formData.append('image_url', imageUrl);
    formData.append('quantity', quantity);
    formData.append('selected_variants', JSON.stringify(selectedVariants));

    fetch('{{ route("mini.cart.add") }}', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(() => {
      // Muvaffaqiyatli qo'shildi - toast
      if (window.showMiniToast) showMiniToast('Mahsulot savatga qo\'shildi!', 'success');
    })
    .catch(error => {
      console.error('Xato:', error);
      if (window.showMiniToast) showMiniToast('Xato yuz berdi, qayta urinib ko\'ring', 'danger');
    });
  }

  // Buyurtma berish funksiyasi
  function checkout() {
    // Enforce variant selection if variants exist
    if (skuProps && skuProps.length){
      const requiredGroups = skuProps.map(p => (p.name || p.prop || 'Option'));
      for (const g of requiredGroups){
        if (!selections[g]){
          if (window.showMiniToast) showMiniToast(`Iltimos, variant tanlang: ${g}`, 'warning');
          return;
        }
      }
    }
    window.location.href = '{{ route("mini.checkout") }}';
  }
</script>
@endsection


