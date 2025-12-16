@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-slate-100 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 flex items-center gap-2">
                    <span
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-emerald-500 text-white shadow-sm">
                        <i class="fas fa-boxes-stacked text-sm"></i>
                    </span>
                    Add Product to Inventory
                </h1>
                <p class="mt-1 text-xs md:text-sm text-slate-500">
                    Choose a category, product, and variety to register a new item in your inventory.
                </p>
            </div>
            <a href="{{ route('inventory.index') }}"
               class="hidden sm:inline-flex items-center gap-2 text-xs md:text-sm text-slate-600 hover:text-slate-900">
                <i class="fas fa-arrow-left"></i>
                Back to Inventory
            </a>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-red-700 text-sm shadow-sm">
                <div class="flex">
                    <div class="mt-0.5">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                    </div>
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Card --}}
        <div
            class="relative overflow-hidden rounded-2xl border border-slate-100 bg-white/90 shadow-lg shadow-emerald-500/5 backdrop-blur-sm">
            <div class="absolute -right-12 -top-12 h-32 w-32 rounded-full bg-emerald-100/60 blur-2xl"></div>
            <div class="absolute -bottom-12 -left-12 h-32 w-32 rounded-full bg-sky-100/60 blur-2xl"></div>

            <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data"
                  class="relative p-6 md:p-7 space-y-6">
                @csrf

                {{-- Category & product --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Category / Type
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-layer-group text-slate-400 text-xs"></i>
                            <select name="Category" id="category"
                                    class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                    required>
                                <option value="">Select Category</option>
                                <option value="Tropical">Tropical</option>
                                <option value="Citrus">Citrus</option>
                                <option value="Stone Fruit">Stone Fruit</option>
                                <option value="Berries">Berries</option>
                                <option value="Melons">Melons</option>
                                <option value="Pome Fruit">Pome Fruit</option>
                                <option value="Exotic">Exotic</option>
                                <option value="Dried Fruit">Dried Fruit</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Product Name
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-tag text-slate-400 text-xs"></i>
                            <select name="Product_Name" id="product_name"
                                    class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                    required>
                                <option value="">Select Category First</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Variety & description --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Variety
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-seedling text-slate-400 text-xs"></i>
                            <select name="variety" id="variety"
                                    class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none">
                                <option value="">Select Product First</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Description <span class="text-[10px] font-normal text-slate-400">(optional)</span>
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <textarea name="description" rows="3"
                                      class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none resize-none"
                                      placeholder="3–5 kg size, ripeness, sweetness, notes, etc."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Image & supplier --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Image <span class="text-[10px] font-normal text-slate-400">(optional)</span>
                        </label>
                        <div
                            class="rounded-xl border border-dashed border-slate-300 bg-slate-50/60 px-3 py-4 flex flex-col items-center justify-center text-center text-xs text-slate-500">
                            <i class="fas fa-image text-slate-400 text-lg mb-1"></i>
                            <p class="mb-2">
                                Upload a clear photo for inventory and POS display.
                            </p>
                            <label
                                class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-3 py-1.5 text-[11px] font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                                <i class="fas fa-upload text-slate-500 text-xs"></i>
                                <span>Choose file</span>
                                <input type="file" name="image"
                                       accept="image/png,image/jpeg,image/jpg"
                                       class="hidden">
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-slate-600 mb-1.5">
                            Supplier
                        </label>
                        <div
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 flex items-center gap-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100 transition">
                            <i class="fas fa-truck-loading text-slate-400 text-xs"></i>
                            <select name="Supplier_ID"
                                    class="w-full bg-transparent text-sm text-slate-800 border-none focus:ring-0 focus:outline-none"
                                    required>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->Supplier_ID }}">
                                        {{ $supplier->Supplier_Name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div class="rounded-xl border-l-4 border-amber-400 bg-amber-50 px-4 py-3 text-xs md:text-sm text-amber-800">
                    <div class="flex gap-2">
                        <i class="fas fa-info-circle mt-0.5"></i>
                        <p>
                            <strong>Note:</strong> After creating the product, go to
                            <span class="font-semibold">Stock‑In</span> to add initial stock quantities and costs.
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <a href="{{ route('inventory.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2 text-xs md:text-sm font-medium text-slate-600 hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-2 text-xs md:text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                        <i class="fas fa-plus-circle mr-2 text-[11px]"></i>
                        Add Product
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    const productsData = {
        'Tropical': {
            'Mango': ['Carabao', 'Piko', 'Indian', 'Apple', 'Katchamita', 'Other'],
            'Banana': ['Lakatan', 'Latundan', 'Saba', 'Señorita', 'Cavendish', 'Cardaba', 'Other'],
            'Pineapple': ['Queen', 'Hawaiian', 'MD2', 'Formosa', 'Other'],
            'Papaya': ['Solo', 'Red Lady', 'Sinta', 'Other'],
            'Coconut': ['Young', 'Mature', 'Other'],
            'Guava': ['Apple', 'Pear', 'Pink', 'White', 'Other'],
            'Jackfruit': ['Sweet', 'Crispy', 'Other'],
            'Durian': ['Puyat', 'Arancillo', 'Native', 'Duyaya', 'Other'],
            'Lychee': ['Mauritius', 'Brewster', 'Other'],
            'Rambutan': ['Red', 'Yellow', 'Other'],
            'Mangosteen': ['Thai', 'Philippine', 'Other'],
            'Dragon Fruit': ['White', 'Red', 'Yellow', 'Other'],
            'Passion Fruit': ['Purple', 'Yellow', 'Other'],
            'Star Apple': ['Purple', 'Green', 'Other'],
            'Sugar Apple': ['Thai', 'Philippine', 'Other'],
            'Soursop': ['Regular', 'Other'],
            'Langsat': ['Regular', 'Other'],
            'Santol': ['Yellow', 'Red', 'Other']
        },
        'Citrus': {
            'Orange': ['Valencia', 'Navel', 'Blood', 'Seville', 'Other'],
            'Calamansi': ['Regular', 'Other'],
            'Lemon': ['Eureka', 'Meyer', 'Lisbon', 'Other'],
            'Lime': ['Persian', 'Key', 'Kaffir', 'Other'],
            'Pomelo': ['Pink', 'White', 'Honey', 'Other'],
            'Mandarin': ['Clementine', 'Tangerine', 'Satsuma', 'Other'],
            'Grapefruit': ['Ruby Red', 'White', 'Pink', 'Other'],
            'Dalandan': ['Regular', 'Other'],
            'Suha': ['Regular', 'Other']
        },
        'Stone Fruit': {
            'Peach': ['Yellow', 'White', 'Donut', 'Other'],
            'Nectarine': ['Yellow', 'White', 'Other'],
            'Plum': ['Red', 'Black', 'Yellow', 'Other'],
            'Apricot': ['Regular', 'Other'],
            'Cherry': ['Sweet', 'Sour', 'Bing', 'Rainier', 'Other']
        },
        'Berries': {
            'Strawberry': ['Sweet Charlie', 'Camarosa', 'Festival', 'Albion', 'Other'],
            'Blueberry': ['Highbush', 'Lowbush', 'Rabbiteye', 'Other'],
            'Raspberry': ['Red', 'Black', 'Golden', 'Other'],
            'Blackberry': ['Thornless', 'Thorny', 'Other'],
            'Cranberry': ['Regular', 'Other'],
            'Gooseberry': ['Green', 'Red', 'Other'],
            'Mulberry': ['White', 'Black', 'Red', 'Other']
        },
        'Melons': {
            'Watermelon': ['Seedless', 'Seeded', 'Yellow', 'Mini', 'Other'],
            'Cantaloupe': ['Regular', 'Other'],
            'Honeydew': ['Green', 'Orange', 'Other'],
            'Muskmelon': ['Regular', 'Other'],
            'Korean Melon': ['Regular', 'Other']
        },
        'Pome Fruit': {
            'Apple': ['Fuji', 'Gala', 'Granny Smith', 'Red Delicious', 'Golden Delicious', 'Honeycrisp', 'Other'],
            'Pear': ['Bartlett', 'Asian', 'Bosc', 'Anjou', 'Other'],
            'Quince': ['Regular', 'Other']
        },
        'Exotic': {
            'Kiwi': ['Green', 'Gold', 'Other'],
            'Persimmon': ['Fuyu', 'Hachiya', 'Other'],
            'Fig': ['Black Mission', 'Brown Turkey', 'Kadota', 'Other'],
            'Pomegranate': ['Wonderful', 'Red Silk', 'Other'],
            'Avocado': ['Hass', 'Fuerte', 'Bacon', 'Other'],
            'Star Fruit': ['Sweet', 'Sour', 'Other'],
            'Custard Apple': ['Regular', 'Other'],
            'Acai': ['Regular', 'Other'],
            'Goji Berry': ['Regular', 'Other'],
            'Miracle Fruit': ['Regular', 'Other']
        },
        'Dried Fruit': {
            'Raisins': ['Golden', 'Dark', 'Sultana', 'Other'],
            'Dates': ['Medjool', 'Deglet Noor', 'Barhi', 'Other'],
            'Prunes': ['Regular', 'Other'],
            'Dried Mango': ['Regular', 'Other'],
            'Dried Papaya': ['Regular', 'Other'],
            'Dried Pineapple': ['Regular', 'Other'],
            'Dried Banana': ['Regular', 'Other'],
            'Dried Coconut': ['Shredded', 'Chips', 'Other'],
            'Dried Cranberry': ['Regular', 'Other'],
            'Dried Apricot': ['Regular', 'Other']
        },
        'Other': {
            'Custom Product': ['Custom Variety']
        }
    };

    const categorySelect = document.getElementById('category');
    const productSelect = document.getElementById('product_name');
    const varietySelect = document.getElementById('variety');

    categorySelect.addEventListener('change', function() {
        const selectedCategory = this.value;

        productSelect.innerHTML = '<option value="">Select Product</option>';
        varietySelect.innerHTML = '<option value="">Select Product First</option>';

        if (selectedCategory && productsData[selectedCategory]) {
            Object.keys(productsData[selectedCategory]).forEach(product => {
                const option = document.createElement('option');
                option.value = product;
                option.textContent = product;
                productSelect.appendChild(option);
            });
        }
    });

    productSelect.addEventListener('change', function() {
        const selectedCategory = categorySelect.value;
        const selectedProduct = this.value;

        varietySelect.innerHTML = '<option value="">Select Variety</option>';

        if (selectedCategory && selectedProduct && productsData[selectedCategory][selectedProduct]) {
            productsData[selectedCategory][selectedProduct].forEach(variety => {
                const option = document.createElement('option');
                option.value = variety;
                option.textContent = variety;
                varietySelect.appendChild(option);
            });
        }
    });
</script>
@endsection
