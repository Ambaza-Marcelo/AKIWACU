 @php
     $usr = Auth::guard('admin')->user();
 @endphp
 <div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <h2 class="text-white">AKIWACU</h2> 
            </a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">

                    @if ($usr->can('dashboard.view'))
                    <li class="active">
                        <a href="{{ route('admin.dashboard') }}" aria-expanded="true"><i class="ti-dashboard"></i><span>@lang('messages.dashboard')</span></a>
                    </li>
                    @endif
                    @if ($usr->can('material.view'))
                    <li>
                        <li class="active"><a href="#"><i class="fa fa-first-order"></i>&nbsp;@lang('EDEN GARDEN')</a></li>
                    </li>
                    <hr>
                    @endif
                    @if ($usr->can('food.create') || $usr->can('food.view') ||  $usr->can('food.edit') ||  $usr->can('food.delete') || $usr->can('drink.create') || $usr->can('drink.view') ||  $usr->can('drink.edit') ||  $usr->can('drink.delete') || $usr->can('material.create') || $usr->can('material.view') ||  $usr->can('material.edit') ||  $usr->can('material.delete') ||  $usr->can('employe.create') || $usr->can('employe.view') ||  $usr->can('employe.edit') ||  $usr->can('employe.delete') || $usr->can('supplier.create') || $usr->can('supplier.view') ||  $usr->can('supplier.edit') ||  $usr->can('supplier.delete') || $usr->can('barrist_item.create') || $usr->can('barrist_item.view') ||  $usr->can('barrist_item.edit') ||  $usr->can('barrist_item.delete') || $usr->can('food_item.create') || $usr->can('food_item.view') ||  $usr->can('food_item.edit') ||  $usr->can('food_item.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-basket"></i><span>
                            @lang('messages.basic_file')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('employe.view'))
                                <li class=""><a href="{{ route('admin.employes.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Employés')</a></li>
                                @endif
                                @if ($usr->can('employe.view'))
                                <li class=""><a href="{{ route('admin.positions.index') }}"><i class="fa fa-map-marker"></i>&nbsp;@lang('Position')</a></li>
                                @endif
                                @if ($usr->can('address.view'))
                                <li class=""><a href="{{ route('admin.addresses.index') }}"><i class="fa fa-map-marker"></i>&nbsp;@lang('messages.address')</a></li>
                                @endif
                                @if($usr->can('supplier.view'))
                                <li class=""><a href="{{ route('admin.suppliers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('messages.suppliers')</a></li>
                                @endif
                                @if($usr->can('booking_client.view'))
                                <li class=""><a href="{{ route('admin.clients.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Clients')</a></li>
                                @endif
                                @if($usr->can('table.view'))
                                <li class=""><a href="{{ route('admin.tables.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Table')</a></li>
                                @endif
                                @if($usr->can('drink_category.view'))
                                <li class=""><a href="{{ route('admin.drink-category.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Categorie Boisson')</a></li>
                                @endif
                                @if($usr->can('food_category.view'))
                                <li class=""><a href="{{ route('admin.food-category.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Categorie Nourriture')</a></li>
                                @endif
                                @if($usr->can('material_category.view'))
                                <li class=""><a href="{{ route('admin.material-category.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Categorie Materiel')</a></li>
                                @endif
                                @if($usr->can('drink.view'))
                                <li class=""><a href="{{ route('admin.drinks.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Boisson')</a></li>
                                @endif
                                @if($usr->can('food.view'))
                                <li class=""><a href="{{ route('admin.foods.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Nourriture')</a></li>
                                @endif
                                @if($usr->can('material.view'))
                                <li class=""><a href="{{ route('admin.materials.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiel')</a></li>
                                @endif
                                @if($usr->can('barrist_item.view'))
                                <li class=""><a href="{{ route('admin.ingredients.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Ingredient')</a></li>
                                @endif
                                @if($usr->can('barrist_item.view'))
                                <li class=""><a href="{{ route('admin.barrist-items.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Barrist Item')</a></li>
                                @endif
                                @if($usr->can('food_item.view'))
                                <li class=""><a href="{{ route('admin.accompagnements.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Accompagnement')</a></li>
                                @endif
                                @if($usr->can('food_item.view'))
                                <li class=""><a href="{{ route('admin.food-items.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Food Item')</a></li>
                                @endif

                                @if($usr->can('drink.view'))
                                <li class=""><a href="{{ route('admin.bartender-items.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Bartender Item')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('drink_big_store.create') || $usr->can('drink_big_store.view') ||  $usr->can('drink_big_store.edit') ||  $usr->can('drink_big_store.delete') || $usr->can('drink_small_store.create') || $usr->can('drink_small_store.view') ||  $usr->can('drink_small_store.edit') ||  $usr->can('drink_small_store.delete') || $usr->can('food_extra_big_store.create') || $usr->can('food_extra_big_store.view') ||  $usr->can('food_extra_big_store.edit') ||  $usr->can('food_extra_big_store.delete') || $usr->can('food_big_store.create') || $usr->can('food_big_store.view') ||  $usr->can('food_big_store.edit') ||  $usr->can('food_big_store.delete') || $usr->can('food_small_store.create') || $usr->can('food_small_store.view') ||  $usr->can('food_small_store.edit') ||  $usr->can('food_small_store.delete') || $usr->can('material_extra_big_store.create') || $usr->can('material_extra_big_store.view') ||  $usr->can('material_extra_big_store.edit') ||  $usr->can('material_extra_big_store.delete') || $usr->can('material_big_store.create') || $usr->can('material_big_store.view') ||  $usr->can('material_big_store.edit') ||  $usr->can('material_big_store.delete') || $usr->can('material_small_store.create') || $usr->can('material_small_store.view') ||  $usr->can('material_small_store.edit') ||  $usr->can('material_small_store.delete') || $usr->can('barrist_production_store.create') || $usr->can('barrist_production_store.view') ||  $usr->can('barrist_production_store.edit') ||  $usr->can('barrist_production_store.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-basket"></i><span>
                            @lang('messages.stock')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('drink_extra_big_store.view'))
                                <li class=""><a href="{{ route('admin.drink-extra-big-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Boissons (Grand)')</a></li>
                                @endif
                                @if($usr->can('drink_big_store.view'))
                                <li class=""><a href="{{ route('admin.drink-big-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Boissons (Intermediaire)')</a></li>
                                @endif
                                @if($usr->can('drink_small_store.view'))
                                <li class=""><a href="{{ route('admin.drink-small-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Boissons (Petit)')</a></li>
                                @endif
                                @if($usr->can('food_extra_big_store.view'))
                                <li class=""><a href="{{ route('admin.food-extra-big-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Nourritures (Grand)')</a></li>
                                @endif
                                @if($usr->can('food_big_store.view'))
                                <li class=""><a href="{{ route('admin.food-big-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Nourritures (Intermediaire)')</a></li>
                                @endif
                                @if($usr->can('food_small_store.view'))
                                <li class=""><a href="{{ route('admin.food-small-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Nourritures (Petit)')</a></li>
                                @endif
                                @if($usr->can('material_extra_big_store.view'))
                                <li class=""><a href="{{ route('admin.material-extra-big-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels (Grand)')</a></li>
                                @endif
                                @if($usr->can('material_big_store.view'))
                                <li class=""><a href="{{ route('admin.material-big-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels (Intermediaire)')</a></li>
                                @endif
                                @if($usr->can('material_small_store.view'))
                                <li class=""><a href="{{ route('admin.material-small-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels (Petit)')</a></li>
                                @endif
                                <!--
                                @if($usr->can('barrist_production_store.view'))
                                <li class=""><a href="{{ route('admin.barrist-production-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Barrist Store')</a></li>
                                @endif -->
                                @if($usr->can('bartender_production_store.view'))
                                <li class=""><a href="{{ route('admin.bartender-production-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Bartender Store')</a></li>
                                @endif
                                @if($usr->can('private_store_item.view'))
                                <li class=""><a href="{{ route('admin.private-store-items.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Private Stock')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('drink_extra_big_inventory.create') || $usr->can('drink_extra_big_inventory.view') ||  $usr->can('drink_extra_big_inventory.edit') ||  $usr->can('drink_extra_big_inventory.delete') || $usr->can('drink_big_inventory.create') || $usr->can('drink_big_inventory.view') ||  $usr->can('drink_big_inventory.edit') ||  $usr->can('drink_big_inventory.delete') || $usr->can('drink_small_inventory.create') || $usr->can('drink_small_inventory.view') ||  $usr->can('drink_small_inventory.edit') ||  $usr->can('drink_small_inventory.delete') || $usr->can('food_extra_big_inventory.create') || $usr->can('food_extra_big_inventory.view') ||  $usr->can('food_extra_big_inventory.edit') ||  $usr->can('food_extra_big_inventory.delete') || $usr->can('food_big_inventory.create') || $usr->can('food_big_inventory.view') ||  $usr->can('food_big_inventory.edit') ||  $usr->can('food_big_inventory.delete') || $usr->can('food_small_inventory.create') || $usr->can('food_small_inventory.view') ||  $usr->can('food_small_inventory.edit') ||  $usr->can('food_small_inventory.delete') || $usr->can('material_extra_big_inventory.create') || $usr->can('material_extra_big_inventory.view') ||  $usr->can('material_extra_big_inventory.edit') ||  $usr->can('material_extra_big_inventory.delete') || $usr->can('material_big_inventory.create') || $usr->can('material_big_inventory.view') ||  $usr->can('material_big_inventory.edit') ||  $usr->can('material_big_inventory.delete') || $usr->can('material_small_inventory.create') || $usr->can('material_small_inventory.view') ||  $usr->can('material_small_inventory.edit') ||  $usr->can('material_small_inventory.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.inventory')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('drink_extra_big_inventory.view'))
                                <li class=""><a href="{{ route('admin.drink-extra-big-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Boissons (Grand)')</a></li>
                                @endif
                                @if($usr->can('drink_big_inventory.view'))
                                <li class=""><a href="{{ route('admin.drink-big-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Boissons (Intermediaire)')</a></li>
                                @endif
                                @if($usr->can('drink_small_inventory.view'))
                                <li class=""><a href="{{ route('admin.drink-small-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Boissons (Petit)')</a></li>
                                @endif
                                @if($usr->can('food_extra_big_inventory.view'))
                                <li class=""><a href="{{ route('admin.food-extra-big-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Nourritures (Grand)')</a></li>
                                @endif
                                @if($usr->can('food_big_inventory.view'))
                                <li class=""><a href="{{ route('admin.food-big-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Nourritures (Intermediaire)')</a></li>
                                @endif
                                @if($usr->can('food_small_inventory.view'))
                                <li class=""><a href="{{ route('admin.food-small-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Nourritures (Petit)')</a></li>
                                @endif
                                @if($usr->can('material_extra_big_inventory.view'))
                                <li class=""><a href="{{ route('admin.material-extra-big-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Materiels (Grand)')</a></li>
                                @endif
                                @if($usr->can('material_big_inventory.view'))
                                <li class=""><a href="{{ route('admin.material-big-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Materiels (Intermediaire)')</a></li>
                                @endif
                                @if($usr->can('material_small_inventory.view'))
                                <li class=""><a href="{{ route('admin.material-small-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Materiels (Petit)')</a></li>
                                @endif
                                @if($usr->can('private_drink_inventory.view'))
                                <li class=""><a href="{{ route('admin.private-drink-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Private Stock')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('drink_requisition.create') || $usr->can('drink_requisition.view') ||  $usr->can('drink_requisition.edit') ||  $usr->can('drink_requisition.delete') || $usr->can('food_requisition.create') || $usr->can('food_requisition.view') ||  $usr->can('food_requisition.edit') ||  $usr->can('food_requisition.delete') || $usr->can('material_requisition.create') || $usr->can('material_requisition.view') ||  $usr->can('material_requisition.edit') ||  $usr->can('material_requisition.delete') || $usr->can('barrist_requisition.create') || $usr->can('barrist_requisition.view') ||  $usr->can('barrist_requisition.edit') ||  $usr->can('barrist_requisition.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.requisition')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('drink_requisition.view'))
                                <li class=""><a href="{{ route('admin.drink-requisitions.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Boissons')</a></li>
                                @endif
                                @if($usr->can('food_requisition.view'))
                                <li class=""><a href="{{ route('admin.food-requisitions.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Nourritures')</a></li>
                                @endif
                                @if($usr->can('material_requisition.view'))
                                <li class=""><a href="{{ route('admin.material-requisitions.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels')</a></li>
                                @endif
                                @if($usr->can('barrist_requisition.view'))
                                <li class=""><a href="{{ route('admin.barrist-requisitions.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Barrist')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('drink_transfer.create') || $usr->can('drink_transfer.view') ||  $usr->can('drink_transfer.edit') ||  $usr->can('drink_transfer.delete') || $usr->can('food_transfer.create') || $usr->can('food_transfer.view') ||  $usr->can('food_transfer.edit') ||  $usr->can('food_transfer.delete') || $usr->can('material_transfer.create') || $usr->can('material_transfer.view') ||  $usr->can('material_transfer.edit') ||  $usr->can('material_transfer.delete') || $usr->can('barrist_transfer.create') || $usr->can('barrist_transfer.view') ||  $usr->can('barrist_transfer.edit') ||  $usr->can('barrist_transfer.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.transfer')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('drink_transfer.view'))
                                <li class=""><a href="{{ route('admin.drink-transfers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Boissons')</a></li>
                                @endif
                                @if($usr->can('food_transfer.view'))
                                <li class=""><a href="{{ route('admin.food-transfers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Nourritures')</a></li>
                                @endif
                                @if($usr->can('material_transfer.view'))
                                <li class=""><a href="{{ route('admin.material-transfers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels')</a></li>
                                @endif
                                @if($usr->can('barrist_transfer.view'))
                                <li class=""><a href="{{ route('admin.barrist-transfers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Barrist')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('drink_return.create') || $usr->can('drink_return.view') ||  $usr->can('drink_return.edit') ||  $usr->can('drink_return.delete') || $usr->can('food_return.create') || $usr->can('food_return.view') ||  $usr->can('food_return.edit') ||  $usr->can('food_return.delete') || $usr->can('material_return.create') || $usr->can('material_return.view') ||  $usr->can('material_return.edit') ||  $usr->can('material_return.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.return')
                        </span></a>
                        <ul class="collapse">
                            <!--
                                @if($usr->can('drink_return.view'))
                                <li class=""><a href=""><i class="fa fa-male"></i>&nbsp;@lang('Boissons')</a></li>
                                @endif
                                @if($usr->can('food_return.view'))
                                <li class=""><a href=""><i class="fa fa-male"></i>&nbsp;@lang('Nourritures')</a></li>
                                @endif
                            -->
                                @if($usr->can('material_return.view'))
                                <li class=""><a href="{{ route('admin.material-return.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ( $usr->can('drink_supplier_order.create') || $usr->can('drink_supplier_order.view') ||  $usr->can('drink_supplier_order.edit') ||  $usr->can('drink_supplier_order.delete') ||  $usr->can('food_supplier_order.view') ||  $usr->can('food_supplier_order.create') ||  $usr->can('material_supplier_order.view') ||  $usr->can('material_supplier_order.create'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-cart"></i><span>
                            @lang('messages.purchases')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('drink_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.drink-purchases.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Demande Achat Boisson')</a></li>
                                @endif
                                @if ($usr->can('food_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.food-purchases.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Demande Achat Nourriture')</a></li>
                                @endif
                                @if ($usr->can('material_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.material-purchases.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Demande Achat Materiel')</a></li>
                                @endif
                                @if ($usr->can('drink_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.drink-supplier-orders.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Commande Boissons')</a></li>
                                @endif
                                @if ($usr->can('food_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.food-supplier-orders.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Commande Nourritures')</a></li>
                                @endif
                                @if ($usr->can('material_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.material-supplier-orders.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Commande Materiel')</a></li>
                                @endif
                                @if ($usr->can('drink_reception.view'))
                                <li class=""><a href="{{ route('admin.drink-receptions.index') }}"><i class="fa fa-shopping-basket"></i>&nbsp;@lang('Reception Boissons')</a></li>
                                @endif
                                @if ($usr->can('food_reception.view'))
                                <li class=""><a href="{{ route('admin.food-receptions.index') }}"><i class="fa fa-shopping-basket"></i>&nbsp;@lang('Reception Nourritures')</a></li>
                                @endif
                                @if ($usr->can('material_reception.view'))
                                <li class=""><a href="{{ route('admin.material-receptions.index') }}"><i class="fa fa-shopping-basket"></i>&nbsp;@lang('Reception Materiel')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ( $usr->can('invoice_drink.create') || $usr->can('invoice_drink.view') ||  $usr->can('invoice_drink.edit') ||  $usr->can('invoice_drink.delete') ||  $usr->can('invoice_kitchen.view') ||  $usr->can('invoice_kitchen.create') ||  $usr->can('invoice_kitchen.view') ||  $usr->can('invoice_kitchen.create'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-cart"></i><span>
                            @lang('messages.sales')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('invoice_drink.view'))
                                <li class=""><a href="{{ route('ebms_api.invoices.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Boissons')</a></li>
                                @endif
                                @if ($usr->can('invoice_kitchen.view'))
                                <li class=""><a href="{{ route('admin.invoice-kitchens.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Cuisine')</a></li>
                                @endif
                                @if ($usr->can('invoice_drink.view'))
                                <li class=""><a href="{{ route('admin.barrist-invoices.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Barrist')</a></li>
                                @endif
                                @if ($usr->can('invoice_drink.view'))
                                <li class=""><a href="{{ route('admin.bartender-invoices.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Bartender')</a></li>
                                @endif

                                @if ($usr->can('invoice_booking.view'))
                                <li class=""><a href="{{ route('admin.booking-invoices.choose') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Reservations')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif
                    @if ( $usr->can('invoice_booking.edit'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-cart"></i><span>
                            @lang('Credits')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('invoice_booking.view'))
                                <li class=""><a href="{{ route('admin.credit-invoices.list') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Credits')</a></li>
                                @endif
                                <!--
                                @if($usr->can('invoice_booking.edit'))
                                <li class=""><a href="{{ route('admin.credit-payes.list') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Payes')</a></li>
                                @endif
                            -->
                        </ul>
                    </li>
                    @endif
                <!--
                    @if ( $usr->can('invoice_drink.create') || $usr->can('invoice_drink.view') ||  $usr->can('invoice_drink.edit') ||  $usr->can('invoice_drink.delete') ||  $usr->can('invoice_kitchen.view') ||  $usr->can('invoice_kitchen.create') ||  $usr->can('invoice_kitchen.view') ||  $usr->can('invoice_kitchen.create'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-cart"></i><span>
                            @lang('OBR')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('invoice_drink.view'))
                                <li class=""><a href="{{ route('ebms_api.invoices.listAll') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Factures')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif
                -->

                    @if ($usr->can('drink_stockin.create') || $usr->can('drink_stockin.view') ||  $usr->can('drink_stockin.edit') ||  $usr->can('drink_stockin.delete') || $usr->can('food_stockin.create') || $usr->can('food_stockin.view') ||  $usr->can('food_stockin.edit') ||  $usr->can('food_stockin.delete') || $usr->can('material_stockin.create') || $usr->can('material_stockin.view') ||  $usr->can('material_stockin.edit') ||  $usr->can('material_stockin.delete') || $usr->can('drink_stockout.create') || $usr->can('drink_stockout.view') ||  $usr->can('drink_stockout.edit') ||  $usr->can('drink_stockout.delete') || $usr->can('food_stockout.create') || $usr->can('food_stockout.view') ||  $usr->can('food_stockout.edit') ||  $usr->can('food_stockout.delete') || $usr->can('material_stockout.create') || $usr->can('material_stockout.view') ||  $usr->can('material_stockout.edit') ||  $usr->can('material_stockout.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.stockin') / @lang('messages.stockout')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('drink_stockin.view'))
                                <li class=""><a href="{{ route('admin.drink-stockins.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Entree Boissons')</a></li>
                                @endif
                                @if($usr->can('food_stockin.view'))
                                <li class=""><a href="{{ route('admin.food-stockins.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Entree Nourritures')</a></li>
                                @endif
                                @if($usr->can('material_stockin.view'))
                                <li class=""><a href="{{ route('admin.material-stockins.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Entree Materiels')</a></li>
                                @endif
                                @if($usr->can('private_drink_stockin.view'))
                                <li class=""><a href="{{ route('admin.private-drink-stockins.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stockin Private Item')</a></li>
                                @endif
                                @if($usr->can('drink_stockout.view'))
                                <li class=""><a href="{{ route('admin.drink-stockouts.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Sortie Boissons')</a></li>
                                @endif
                                @if($usr->can('food_stockout.view'))
                                <li class=""><a href="{{ route('admin.food-stockouts.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Sortie Nourritures')</a></li>
                                @endif
                                @if($usr->can('material_stockout.view'))
                                <li class=""><a href="{{ route('admin.material-stockouts.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Sortie Materiels')</a></li>
                                @endif
                                @if($usr->can('private_drink_stockout.view'))
                                <li class=""><a href="{{ route('admin.private-drink-stockouts.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stockout Private Item')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    <!-- human resource management -->
                    @if ($usr->can('booking_service.create') || $usr->can('booking_service.view') ||  $usr->can('booking_service.edit') ||  $usr->can('booking_service.delete') || $usr->can('booking_salle.create') || $usr->can('booking_salle.view') ||  $usr->can('booking_salle.edit') ||  $usr->can('booking_salle.delete') || $usr->can('booking_technique.create') || $usr->can('booking_technique.view') ||  $usr->can('booking_technique.edit') ||  $usr->can('booking_technique.delete') || $usr->can('booking_client.create') || $usr->can('booking_client.view') ||  $usr->can('booking_client.edit') ||  $usr->can('booking_client.delete') || $usr->can('booking.create') || $usr->can('booking.view') ||  $usr->can('booking.edit') ||  $usr->can('booking.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                            @lang('RESERVATIONS')
                        </span></a>
                        <ul class="">
                            
                            @if ($usr->can('booking_technique.view'))
                                <li class=""><a href="{{ route('admin.techniques.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Techniques')</a></li>
                            @endif
                            @if ($usr->can('booking_salle.view'))
                                <li class=""><a href="{{ route('admin.salles.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Salles')</a></li>
                            @endif
                            @if ($usr->can('booking_service.view'))
                                <li class=""><a href="{{ route('admin.services.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Services')</a></li>
                            @endif
                            @if ($usr->can('booking.view'))
                                <li class=""><a href="{{ route('admin.kidness-spaces.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Kidness Space')</a></li>
                            @endif
                            @if ($usr->can('booking.view'))
                                <li class=""><a href="{{ route('admin.break-fasts.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Break Fast')</a></li>
                            @endif
                            @if ($usr->can('booking.view'))
                                <li class=""><a href="{{ route('admin.swiming-pools.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Swiming Pool')</a></li>
                            @endif
                            @if ($usr->can('booking_salle.view'))
                                <li class=""><a href="{{ route('admin.booking-clients.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Clients')</a></li>
                            @endif

                            @if ($usr->can('booking.view'))
                                <li class=""><a href="{{ route('admin.booking-salles.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Reservation Salles')</a></li>
                            @endif
                            @if ($usr->can('booking.view'))
                                <li class=""><a href="{{ route('admin.booking-services.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Reservation Services')</a></li>
                            @endif
                            @if ($usr->can('booking.view'))
                                <li class=""><a href=""><i class="fa fa-user"></i>&nbsp;@lang('Reservation Tables')</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('drink_extra_big_report.view') || $usr->can('drink_big_report.view') || $usr->can('drink_small_report.view') || $usr->can('food_extra_big_report.view') || $usr->can('food_big_report.view') || $usr->can('food_small_report.view') || $usr->can('material_extra_big_report.view') || $usr->can('material_big_report.view') || $usr->can('material_small_report.view') || $usr->can('barrist_report.view'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-bar-chart"></i><span>
                            @lang('messages.stock_report')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('drink_extra_big_report.view'))
                                <li class=""><a href="{{ route('admin.drink-extra-big-store-report.index') }}">@lang('Grand stock boisson ')</a></li>
                                @endif
                                @if ($usr->can('drink_big_report.view'))
                                <li class=""><a href="{{ route('admin.drink-big-store-report.index') }}">@lang('Intermediaire stock boisson ')</a></li>
                                @endif
                                @if ($usr->can('drink_small_report.view'))
                                <li class=""><a href="{{ route('admin.drink-small-store-report.index') }}">@lang('Petit stock boisson ')</a></li>
                                @endif
                                @if ($usr->can('food_extra_big_report.view'))
                                <li class=""><a href="{{ route('admin.food-extra-big-store-report.index') }}">@lang('Grand stock nourritures ')</a></li>
                                @endif
                                @if ($usr->can('food_big_report.view'))
                                <li class=""><a href="{{ route('admin.food-big-store-report.index') }}">@lang('Intermidiaire stock nourritures ')</a></li>
                                @endif
                                @if ($usr->can('food_small_report.view'))
                                <li class=""><a href="{{ route('admin.food-small-store-report.index') }}">@lang('Petit stock nourritures ')</a></li>
                                @endif
                                @if ($usr->can('material_extra_big_report.view'))
                                <li class=""><a href="{{ route('admin.material-extra-big-store-report.index') }}">@lang('Grand stock materiel ')</a></li>
                                @endif
                                @if ($usr->can('material_big_report.view'))
                                <li class=""><a href="{{ route('admin.material-big-store-report.index') }}">@lang('Intermediaire stock materiel ')</a></li>
                                @endif
                                @if ($usr->can('material_small_report.view'))
                                <li class=""><a href="{{ route('admin.material-small-store-report.index') }}">@lang('Petit stock materiel ')</a></li>
                                @endif
                                @if ($usr->can('barrist_report.view'))
                                <li class=""><a href="{{ route('admin.barrist-food-big-report.index') }}">@lang('Nourritures vers Barrist ')</a></li>
                                @endif
                                @if ($usr->can('barrist_report.view'))
                                <li class=""><a href="{{ route('admin.barrist-drink-big-report.index') }}">@lang('Boissons vers Barrist ')</a></li>
                                @endif
                                @if ($usr->can('invoice_drink.view'))
                                <li class=""><a href="{{ route('admin.invoice-report.report') }}">@lang('Ventes')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('admin.create') || $usr->can('admin.view') ||  $usr->can('admin.edit') ||  $usr->can('admin.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                            @lang('messages.users')
                        </span></a>
                        <ul class="collapse {{ Route::is('admin.admins.create') || Route::is('admin.admins.index') || Route::is('admin.admins.edit') || Route::is('admin.admins.show') ? 'in' : '' }}">
                            
                            @if ($usr->can('admin.view'))
                                <li class="{{ Route::is('admin.admins.index')  || Route::is('admin.admins.edit') ? 'active' : '' }}"><a href="{{ route('admin.admins.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('messages.users')</a></li>
                            @endif
                            @if ($usr->can('role.view'))
                                <li class="{{ Route::is('admin.roles.index')  || Route::is('admin.roles.edit') ? 'active' : '' }}"><a href="{{ route('admin.roles.index') }}"><i class="fa fa-tasks"></i> &nbsp;@lang('messages.roles') & @lang('messages.permissions')</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if($usr->can('setting.view'))
                    <li class=""><a href="{{ route('admin.settings.index') }}"><i class="fa fa-cogs"></i><span>@lang('messages.setting')</a></li>
                    @endif
                    <hr>

                    <!-- start sotb menu -->

                    @if ($usr->can('sotb_material.view'))
                    <li>
                        <li class="active"><a href="#"><i class="fa fa-first-order"></i>&nbsp;@lang('SOTB')</a></li>
                    </li>
                    <hr>
                    @endif
                    @if ($usr->can('sotb_material.create') || $usr->can('sotb_material.view') ||  $usr->can('sotb_material.edit') ||  $usr->can('sotb_material.delete') || $usr->can('sotb_supplier.create') || $usr->can('sotb_supplier.view') ||  $usr->can('sotb_supplier.edit') ||  $usr->can('sotb_supplier.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-basket"></i><span>
                            @lang('messages.basic_file')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('sotb_material.view'))
                                <li class=""><a href="{{ route('admin.sotb-suppliers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('messages.suppliers')</a></li>
                                @endif
                                @if($usr->can('sotb_material.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-category.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Categorie Materiel')</a></li>
                                @endif
                                @if($usr->can('sotb_material.view'))
                                <li class=""><a href="{{ route('admin.sotb-materials.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiel')</a></li>
                                @endif
                                @if($usr->can('sotb_car.view'))
                                <li class=""><a href="{{ route('admin.sotb-cars.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Car')</a></li>
                                @endif
                                @if($usr->can('sotb_driver.view'))
                                <li class=""><a href="{{ route('admin.sotb-drivers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Driver')</a></li>
                                @endif
                                @if($usr->can('sotb_driver_car.view'))
                                <li class=""><a href="{{ route('admin.sotb-driver-cars.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Driver&amp;Car')</a></li>
                                @endif
                                @if($usr->can('sotb_fuel.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuels.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel')</a></li>
                                @endif
                                @if($usr->can('sotb_index_pump.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-index-pumps.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Index Pump')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('sotb_material_bg_store.create') || $usr->can('sotb_material_bg_store.view') ||  $usr->can('sotb_material_bg_store.edit') ||  $usr->can('sotb_material_bg_store.delete') || $usr->can('sotb_material_md_store.create') || $usr->can('sotb_material_md_store.view') ||  $usr->can('sotb_material_md_store.edit') ||  $usr->can('sotb_material_md_store.delete') || $usr->can('sotb_material_sm_store.create') || $usr->can('sotb_material_sm_store.view') ||  $usr->can('sotb_material_sm_store.edit') ||  $usr->can('sotb_material_sm_store.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-basket"></i><span>
                            @lang('messages.stock')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('sotb_material_bg_store.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-bg-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels (Grand)')</a></li>
                                @endif
                                @if($usr->can('sotb_material_md_store.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-md-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels (Intermediaire)')</a></li>
                                @endif
                                @if($usr->can('sotb_material_sm_store.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-sm-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels (Petit)')</a></li>
                                @endif
                                @if($usr->can('sotb_fuel_pump.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-pumps.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Pump')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('sotb_material_bg_inventory.create') || $usr->can('sotb_material_bg_inventory.view') ||  $usr->can('sotb_material_bg_inventory.edit') ||  $usr->can('sotb_material_bg_inventory.delete') || $usr->can('sotb_material_md_inventory.create') || $usr->can('sotb_material_md_inventory.view') ||  $usr->can('sotb_material_md_inventory.edit') ||  $usr->can('sotb_material_md_inventory.delete') || $usr->can('sotb_material_sm_inventory.create') || $usr->can('sotb_material_sm_inventory.view') ||  $usr->can('sotb_material_sm_inventory.edit') ||  $usr->can('sotb_material_sm_inventory.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.inventory')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('sotb_material_bg_inventory.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-bg-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Materiels (Grand)')</a></li>
                                @endif
                                @if($usr->can('sotb_material_md_inventory.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-md-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Materiels (Intermediaire)')</a></li>
                                @endif
                                @if($usr->can('sotb_material_sm_inventory.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-sm-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Materiels (Petit)')</a></li>
                                @endif
                                @if($usr->can('sotb_fuel_inventory.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-inventories.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Inventory')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('sotb_material_requisition.create') || $usr->can('sotb_material_requisition.view') ||  $usr->can('sotb_material_requisition.edit') ||  $usr->can('sotb_material_requisition.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.requisition')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('sotb_material_requisition.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-requisitions.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels')</a></li>
                                @endif
                                @if($usr->can('sotb_fuel_requisition.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-requisitions.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Requisition')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('sotb_material_transfert.create') || $usr->can('sotb_material_transfert.view') ||  $usr->can('sotb_material_transfert.edit') ||  $usr->can('sotb_material_transfert.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.transfer')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('sotb_material_transfert.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-transferts.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('sotb_material_return.create') || $usr->can('sotb_material_return.view') ||  $usr->can('sotb_material_return.edit') ||  $usr->can('sotb_material_return.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.return')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('sotb_material_return.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-return.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ( $usr->can('sotb_material_reception.create') || $usr->can('sotb_material_reception.view') ||  $usr->can('sotb_material_reception.edit') ||  $usr->can('sotb_material_reception.delete') ||  $usr->can('sotb_material_purchase.view') ||  $usr->can('sotb_material_purchase.create') ||  $usr->can('sotb_material_supplier_order.view') ||  $usr->can('sotb_material_supplier_order.create'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-cart"></i><span>
                            @lang('messages.purchases')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('sotb_material_purchase.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-purchases.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Demande Achat Materiel')</a></li>
                                @endif
                                @if ($usr->can('sotb_fuel_purchase.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-purchases.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Demande Achat Carburant')</a></li>
                                @endif
                                @if ($usr->can('sotb_material_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-supplier-orders.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Commande Materiel')</a></li>
                                @endif
                                @if ($usr->can('sotb_fuel_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-supplier-orders.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Commande Carburant')</a></li>
                                @endif
                                @if ($usr->can('sotb_material_reception.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-receptions.index') }}"><i class="fa fa-shopping-basket"></i>&nbsp;@lang('Reception Materiel')</a></li>
                                @endif
                                @if ($usr->can('sotb_fuel_reception.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-receptions.index') }}"><i class="fa fa-shopping-basket"></i>&nbsp;@lang('Reception Carburant')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('sotb_material_stockin.create') || $usr->can('sotb_material_stockin.view') ||  $usr->can('sotb_material_stockin.edit') ||  $usr->can('sotb_material_stockin.delete') || $usr->can('sotb_material_stockout.create') || $usr->can('sotb_material_stockout.view') ||  $usr->can('sotb_material_stockout.edit') ||  $usr->can('sotb_material_stockout.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.stockin') / @lang('messages.stockout')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('sotb_material_stockin.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-stockins.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Entree Materiels')</a></li>
                                @endif
                                @if($usr->can('sotb_fuel_stockin.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-stockins.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Stockin')</a></li>
                                @endif
                                @if($usr->can('sotb_material_stockout.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-stockouts.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Sortie Materiels')</a></li>
                                @endif
                                @if($usr->can('sotb_fuel_stockout.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-stockouts.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Stockout')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('sotb_material_bg_store_report.view') || $usr->can('sotb_material_md_store_report.view') || $usr->can('sotb_material_sm_store_report.view'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-bar-chart"></i><span>
                            @lang('messages.stock_report')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('sotb_material_bg_store_report.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-bg-store-report.index') }}">@lang('Grand stock materiel ')</a></li>
                                @endif
                                @if ($usr->can('sotb_material_md_store_report.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-md-store-report.index') }}">@lang('Intermediaire stock materiel ')</a></li>
                                @endif
                                @if ($usr->can('sotb_material_sm_store_report.view'))
                                <li class=""><a href="{{ route('admin.sotb-material-sm-store-report.index') }}">@lang('Petit stock materiel ')</a></li>
                                @endif
                                @if ($usr->can('sotb_fuel_report.view'))
                                <li class=""><a href="{{ route('admin.sotb-fuel-report.index') }}">@lang('Fuel Movemment ')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif
                    <!-- end sotb menu -->

                    <!-- start musumba steel menu -->
                    @if ($usr->can('musumba_steel_facture.view'))
                    <hr>
                    <li>
                        <li class="active"><a href="#"><i class="fa fa-first-order"></i>&nbsp;@lang('MUSUMBA STEEL')</a></li>
                    </li>
                    <hr>
                    @endif
                    @if ( $usr->can('musumba_steel_facture.create') || $usr->can('musumba_steel_facture.view') ||  $usr->can('musumba_steel_facture.edit') ||  $usr->can('musumba_steel_facture.delete') ||  $usr->can('musumba_steel_facture.validate') ||  $usr->can('musumba_steel_facture.confirm') ||  $usr->can('musumba_steel_facture.send') ||  $usr->can('musumba_steel_facture.approuve'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-cart"></i><span>
                            @lang('MUSUMBA FACTURES')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('musumba_steel_facture.view'))
                                <li class=""><a href="{{ route('admin.musumba-steel-item-categories.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Categories')</a></li>
                                @endif 
                                @if ($usr->can('musumba_steel_facture.view'))
                                <li class=""><a href="{{ route('admin.musumba-steel-items.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Articles')</a></li>
                                @endif
                                @if ($usr->can('musumba_steel_facture.view'))
                                <li class=""><a href="{{ route('admin.musumba-steel-clients.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Clients')</a></li>
                                @endif
                                @if ($usr->can('musumba_steel_facture.view'))
                                <li class=""><a href="{{ route('admin.musumba-steel-facture.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Factures')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('musumba_steel_car.create') || $usr->can('musumba_steel_car.view') ||  $usr->can('musumba_steel_car.edit') ||  $usr->can('musumba_steel_car.delete') || $usr->can('musumba_steel_material_supplier.create') || $usr->can('musumba_steel_material_supplier.view') ||  $usr->can('musumba_steel_material_supplier.edit') ||  $usr->can('musumba_steel_fuel_supplier.view'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-basket"></i><span>
                            @lang('messages.basic_file')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('musumba_steel_material_supplier.view'))
                                <li class=""><a href="{{ route('admin.ms-material-suppliers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fournisseurs Materiels')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_fuel_supplier.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-suppliers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fournisseurs Carburant')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_material_supplier.view'))
                                <li class=""><a href="{{ route('admin.ms-material-category.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Categorie Materiel')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_material_supplier.view'))
                                <li class=""><a href="{{ route('admin.ms-materials.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiel')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_car.view'))
                                <li class=""><a href="{{ route('admin.ms-cars.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Car')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_driver.view'))
                                <li class=""><a href="{{ route('admin.ms-drivers.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Driver')</a></li>
                                @endif
                                <!--
                                @if($usr->can('musumba_steel_driver_car.view'))
                                <li class=""><a href="{{ route('admin.ms-driver-cars.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Driver&amp;Car')</a></li>
                                @endif
                            -->
                                @if($usr->can('musumba_steel_fuel.view'))
                                <li class=""><a href="{{ route('admin.ms-fuels.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_index_pump.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-index-pumps.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Index Pump')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('musumba_steel_material_store.create') || $usr->can('musumba_steel_material_store.view') ||  $usr->can('musumba_steel_material_store.edit') ||  $usr->can('musumba_steel_material_store.delete') || $usr->can('musumba_steel_fuel_pump.create') || $usr->can('musumba_steel_fuel_pump.view') ||  $usr->can('musumba_steel_fuel_pump.edit') ||  $usr->can('musumba_steel_fuel_pump.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-basket"></i><span>
                            @lang('messages.stock')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('musumba_steel_material_store.view'))
                                <li class=""><a href="{{ route('admin.ms-material-store.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_fuel_pump.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-pumps.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Pump')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('musumba_steel_material_inventory.create') || $usr->can('musumba_steel_material_inventory.view') ||  $usr->can('musumba_steel_material_inventory.edit') ||  $usr->can('musumba_steel_material_inventory.delete') || $usr->can('musumba_steel_fuel_inventory.create') || $usr->can('musumba_steel_fuel_inventory.view') ||  $usr->can('musumba_steel_fuel_inventory.edit') ||  $usr->can('musumba_steel_fuel_inventory.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.inventory')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('musumba_steel_material_inventory.view'))
                                <li class=""><a href="{{ route('admin.ms-material-store-inventory.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Stock Materiels (Grand)')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_fuel_inventory.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-inventories.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Inventory')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('mmusumba_steel_material_requisition.create') || $usr->can('mmusumba_steel_material_requisition.view') ||  $usr->can('mmusumba_steel_material_requisition.edit') ||  $usr->can('mmusumba_steel_material_requisition.delete') || $usr->can('musumba_steel_fuel_requisition.create') || $usr->can('musumba_steel_fuel_requisition.view'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.requisition')
                        </span></a>
                        <ul class="collapse">
                            <!--
                                @if($usr->can('musumba_steel_material_requisition.view'))
                                <li class=""><a href="{{ route('admin.ms-material-requisitions.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Materiels')</a></li>
                                @endif
                            -->
                                @if($usr->can('musumba_steel_fuel_requisition.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-requisitions.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Requisition')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ( $usr->can('musumba_steel_material_reception.create') || $usr->can('musumba_steel_material_reception.view') ||  $usr->can('musumba_steel_material_reception.edit') ||  $usr->can('musumba_steel_material_reception.delete') ||  $usr->can('musumba_steel_material_purchase.view') ||  $usr->can('musumba_steel_material_purchase.create') ||  $usr->can('musumba_steel_material_supplier_order.view') ||  $usr->can('musumba_steel_material_supplier_order.create') ||  $usr->can('musumba_steel_fuel_reception.view') ||  $usr->can('musumba_steel_fuel_supplier_order.view') ||  $usr->can('musumba_steel_fuel_purchase.view'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-shopping-cart"></i><span>
                            @lang('messages.purchases')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('musumba_steel_material_purchase.view'))
                                <li class=""><a href="{{ route('admin.ms-material-purchases.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Demande Achat Materiel')</a></li>
                                @endif
                                @if ($usr->can('musumba_steel_fuel_purchase.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-purchases.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Demande Achat Carburant')</a></li>
                                @endif
                                @if ($usr->can('musumba_steel_material_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.ms-material-supplier-orders.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Commande Materiel')</a></li>
                                @endif
                                @if ($usr->can('musumba_steel_fuel_supplier_order.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-supplier-orders.index') }}"><i class="fa fa-first-order"></i>&nbsp;@lang('Commande Carburant')</a></li>
                                @endif
                                @if ($usr->can('musumba_steel_material_reception.view'))
                                <li class=""><a href="{{ route('admin.ms-material-receptions.index') }}"><i class="fa fa-shopping-basket"></i>&nbsp;@lang('Reception Materiel')</a></li>
                                @endif
                                @if ($usr->can('musumba_steel_fuel_reception.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-receptions.index') }}"><i class="fa fa-shopping-basket"></i>&nbsp;@lang('Reception Carburant')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('musumba_steel_material_stockin.create') || $usr->can('musumba_steel_material_stockin.view') ||  $usr->can('musumba_steel_material_stockin.edit') ||  $usr->can('musumba_steel_material_stockin.delete') || $usr->can('musumba_steel_material_stockout.create') || $usr->can('musumba_steel_material_stockout.view') ||  $usr->can('musumba_steel_material_stockout.edit') ||  $usr->can('musumba_steel_material_stockout.delete') ||  $usr->can('musumba_steel_fuel_stockin.view') ||  $usr->can('musumba_steel_fuel_stockout.view'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-list"></i><span>
                            @lang('messages.stockin') / @lang('messages.stockout')
                        </span></a>
                        <ul class="collapse">
                                @if($usr->can('musumba_steel_material_stockin.view'))
                                <li class=""><a href="{{ route('admin.ms-material-stockins.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Entree Materiels')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_fuel_stockin.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-stockins.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Stockin')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_material_stockout.view'))
                                <li class=""><a href="{{ route('admin.ms-material-stockouts.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Sortie Materiels')</a></li>
                                @endif
                                @if($usr->can('musumba_steel_fuel_stockout.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-stockouts.index') }}"><i class="fa fa-male"></i>&nbsp;@lang('Fuel Stockout')</a></li>
                                @endif

                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('musumba_steel_material_report.view') || $usr->can('musumba_steel_fuel_report.view'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-bar-chart"></i><span>
                            @lang('messages.stock_report')
                        </span></a>
                        <ul class="collapse">
                                @if ($usr->can('musumba_steel_material_report.view'))
                                <li class=""><a href="{{ route('admin.ms-material-store-report.index') }}">@lang('stock materiel ')</a></li>
                                @endif
                                @if ($usr->can('musumba_steel_fuel_report.view'))
                                <li class=""><a href="{{ route('admin.ms-fuel-report.index') }}">@lang('Mouvement Carburant')</a></li>
                                @endif
                        </ul>
                    </li>
                    @endif

                    <!-- human resource management -->
                    @if ($usr->can('hr_employe.create') || $usr->can('hr_employe.view') ||  $usr->can('hr_employe.edit') ||  $usr->can('hr_employe.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                            @lang('Employes')
                        </span></a>
                        <ul class="">
                            @if ($usr->can('hr_departement.view'))
                                <li class=""><a href="{{ route('admin.hr-companies.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Entreprises')</a></li>
                            @endif
                            @if ($usr->can('hr_departement.view'))
                                <li class=""><a href="{{ route('admin.hr-departements.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Département')</a></li>
                            @endif
                            @if ($usr->can('hr_service.view'))
                                <li class=""><a href="{{ route('admin.hr-services.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Services')</a></li>
                            @endif
                            @if ($usr->can('hr_fonction.view'))
                                <li class=""><a href="{{ route('admin.hr-fonctions.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Fonctions')</a></li>
                            @endif
                            @if ($usr->can('hr_grade.view'))
                                <li class=""><a href="{{ route('admin.hr-grades.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Grades')</a></li>
                            @endif
                            @if ($usr->can('hr_banque.view'))
                                <li class=""><a href="{{ route('admin.hr-banques.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Banques')</a></li>
                            @endif
                            @if ($usr->can('hr_employe.view'))
                                <li class=""><a href="{{ route('admin.hr-company.select') }}"><i class="fa fa-user"></i>&nbsp;@lang('Employés')</a></li>
                            @endif
                            
                        </ul>
                    </li>
                    @endif
                    <!-- human resource management -->
                    @if ($usr->can('hr_ecole.create') || $usr->can('hr_ecole.view') ||  $usr->can('hr_filiere.view') ||  $usr->can('hr_filiere.create') || $usr->can('hr_stagiaire.create') || $usr->can('hr_stagiaire.view') ||  $usr->can('hr_stagiaire.edit') ||  $usr->can('hr_stagiaire.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                            @lang('Stagiaires')
                        </span></a>
                        <ul class="">
                            @if ($usr->can('hr_ecole.view'))
                                <li class=""><a href="{{ route('admin.hr-ecoles.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Ecoles')</a></li>
                            @endif
                            @if ($usr->can('hr_filiere.view'))
                                <li class=""><a href="{{ route('admin.hr-filieres.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Filieres')</a></li>
                            @endif
                            @if ($usr->can('hr_stagiaire.view'))
                                <li class=""><a href="{{ route('admin.stagiare-select-by-company') }}"><i class="fa fa-user"></i>&nbsp;@lang('Stagiaires')</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- human resource management -->
                    @if ($usr->can('hr_conge.create') || $usr->can('hr_conge.view') ||  $usr->can('hr_conge.edit') ||  $usr->can('hr_conge.delete') || $usr->can('hr_conge_paye.create') || $usr->can('hr_conge_paye.view') ||  $usr->can('hr_conge_paye.edit') ||  $usr->can('hr_conge_paye.delete') || $usr->can('hr_absence.create') || $usr->can('hr_absence.view') ||  $usr->can('hr_absence.edit') ||  $usr->can('hr_absence.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                            @lang('Conges')
                        </span></a>
                        <ul class="">
                            @if ($usr->can('hr_conge.view'))
                                <li class=""><a href="{{ route('admin.hr-leave-taken.select-by-company') }}"><i class="fa fa-user"></i>&nbsp;@lang('Congés')</a></li>
                            @endif
                            @if ($usr->can('hr_conge_paye.view'))
                                <li class=""><a href="{{ route('admin.hr-take-paid-leave.select-by-company') }}"><i class="fa fa-user"></i>&nbsp;@lang('Congé Payé')</a></li>
                            @endif
                            @if ($usr->can('hr_absence.view'))
                                <li class=""><a href=""><i class="fa fa-user"></i>&nbsp;@lang('Présence&amp;Absence')</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- human resource management -->
                    @if ($usr->can('hr_paiement.create') || $usr->can('hr_paiement.view') ||  $usr->can('hr_paiement.edit') ||  $usr->can('hr_paiement.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                            @lang('Bulletin de paie')
                        </span></a>
                        <ul class="">
                            @if ($usr->can('hr_paiement.view'))
                                <li class=""><a href="{{ route('admin.hr-journal-paies.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Journal de paie')</a></li>
                            @endif
                            @if ($usr->can('hr_paiement.view'))
                                <li class=""><a href="{{ route('admin.hr-paiement.selectByCompany') }}"><i class="fa fa-user"></i>&nbsp;@lang('Bulletin de paie')</a></li>
                            @endif
                            @if ($usr->can('hr_reglage.view'))
                                <li class=""><a href="{{ route('admin.hr-reglages.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Réglages')</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- human resource management -->
                    @if ($usr->can('hr_journal_paie.create') || $usr->can('hr_journal_paie.view') ||  $usr->can('hr_journal_paie.edit') ||  $usr->can('hr_journal_paie.delete') || $usr->can('hr_journal_conge.create') || $usr->can('hr_journal_conge.view') ||  $usr->can('hr_journal_conge.edit') ||  $usr->can('hr_journal_conge.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                            @lang('Les Journaux')
                        </span></a>
                        <ul class="">
                            @if ($usr->can('hr_journal_paie.view'))
                                <li class=""><a href="{{ route('admin.hr-journal-paies.index') }}"><i class="fa fa-user"></i>&nbsp;@lang('Journal Paie')</a></li>
                            @endif
                            @if ($usr->can('hr_journal_paie.view'))
                                <li class=""><a href="{{ route('admin.hr-journal-cotisations.select-by-company') }}"><i class="fa fa-user"></i>&nbsp;@lang('Journal Cotisations')</a></li>
                            @endif
                            @if ($usr->can('hr_journal_paie.view'))
                                <li class=""><a href="{{ route('admin.hr-journal-impots.select-by-company') }}"><i class="fa fa-user"></i>&nbsp;@lang('Journal IRE')</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <!-- end musumba steel menu -->
                </ul>
            </nav>
        </div>
    </div>
</div>
