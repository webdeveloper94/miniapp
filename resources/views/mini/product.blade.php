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
      <h6 class="mb-0">Xususiyatlar</h6>
      <button id="attrsToggle" type="button" class="btn btn-sm btn-outline-primary">Ko'rsatish</button>
    </div>
    <div id="attrsBody" class="d-none"></div>
  </div>

  <div id="variants" class="card mini-card p-3 mb-3 d-none">
    <h6 class="mb-2">Variantlar</h6>
    <div id="variantsBody"></div>
  </div>

  @if(!empty($offerId))
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
  @endif

  <div class="d-grid gap-2">
    <button class="btn btn-mini"><i class="bi bi-cart-plus"></i> Savatga qo‘shish</button>
    <button class="btn btn-mini"><i class="bi bi-lightning-charge"></i> Buyurtma berish</button>
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
  let price = product.price || product.minPrice || product.discountPrice || product.referencePrice;
  const sale = product.productSaleInfo || {};
  const pr = product.priceRange || product.priceRanges || sale.priceRanges || sale.priceRangeList;
  if (!price && Array.isArray(pr) && pr.length){
    const first = pr[0].price ?? pr[0].value; const last = pr.at(-1).price ?? pr.at(-1).value; price = first && last ? `${first} ~ ${last}` : (first ?? last);
  }
  headEl.innerHTML = `<div class="fw-semibold mb-1">${title}</div><div class="mb-1">Narx: <span class="chip">${price ?? '-'}</span></div><small class="text-secondary d-block">Do‘kon: ${shop}</small>`;

  let imgs = pick('images') || pick('imageList') || pick('gallery') || (product && product.productImage && (product.productImage.images || product.productImage.imageList)) || pick('productImage');
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

  const attrs = product.props || product.attributes || product.skuProps || product.attributeList || product.productAttribute || [];
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
      btn.textContent = hidden ? "Ko'rsatish" : "Yopish";
    });
  }

  // Build variants from skuProps or derive from productSkuInfos
  let skuProps = product.skuProps || product.skuAttributes || product.skuPropertyList || [];
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
    const selections = {};
    variantsBody.addEventListener('click', (e)=>{
      const el = e.target.closest('.chip-select');
      if (!el) return;
      const group = el.getAttribute('data-group');
      variantsBody.querySelectorAll(`.chip-select[data-group="${group}"]`).forEach(c=>c.classList.remove('active'));
      el.classList.add('active');
      selections[group] = el.getAttribute('data-value');
    });
  }
</script>
@endsection


