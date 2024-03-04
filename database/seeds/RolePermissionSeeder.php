<?php
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Class RolePermissionSeeder.
 *
 * @see https://spatie.be/docs/laravel-permission/v5/basic-usage/multiple-guards
 *
 * @package App\Database\Seeds
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        // Permission List as array
        $permissions = [

            [
                'group_name' => 'dashboard',
                'permissions' => [
                    'dashboard.view',
                    'dashboard.edit',
                ]
            ],
            [
                'group_name' => 'admin',
                'permissions' => [
                    // admin Permissions
                    'admin.create',
                    'admin.view',
                    'admin.edit',
                    'admin.delete',
                ]
            ],
            [
                'group_name' => 'role',
                'permissions' => [
                    // role Permissions
                    'role.create',
                    'role.view',
                    'role.edit',
                    'role.delete',
                ]
            ],
            [
                'group_name' => 'address',
                'permissions' => [
                    // address Permissions
                    'address.create',
                    'address.view',
                    'address.edit',
                    'address.delete',
                ]
            ],
            [
                'group_name' => 'material',
                'permissions' => [
                    // material Permissions
                    'material.create',
                    'material.view',
                    'material.edit',
                    'material.delete',
                ]
            ],
            [
                'group_name' => 'food_item',
                'permissions' => [
                    // food_item Permissions
                    'food_item.create',
                    'food_item.view',
                    'food_item.edit',
                    'food_item.delete',
                ]
            ],
            [
                'group_name' => 'barrist_item',
                'permissions' => [
                    // barrist_item Permissions
                    'barrist_item.create',
                    'barrist_item.view',
                    'barrist_item.edit',
                    'barrist_item.delete',
                ]
            ],
            [
                'group_name' => 'bartender_item',
                'permissions' => [
                    // bartender_item Permissions
                    'bartender_item.create',
                    'bartender_item.view',
                    'bartender_item.edit',
                    'bartender_item.delete',
                ]
            ],
            [
                'group_name' => 'food',
                'permissions' => [
                    // food Permissions
                    'food.create',
                    'food.view',
                    'food.edit',
                    'food.delete',
                ]
            ],
            [
                'group_name' => 'drink',
                'permissions' => [
                    // drink Permissions
                    'drink.create',
                    'drink.view',
                    'drink.edit',
                    'drink.delete',
                ]
            ],
             [
                'group_name' => 'category',
                'permissions' => [
                    // category Permissions
                    'category.create',
                    'category.view',
                    'category.edit',
                    'category.delete',
                ]
            ],
            [
                'group_name' => 'drink_category',
                'permissions' => [
                    // drink_category Permissions
                    'drink_category.create',
                    'drink_category.view',
                    'drink_category.edit',
                    'drink_category.delete',
                ]
            ],
            [
                'group_name' => 'food_category',
                'permissions' => [
                    // food_category Permissions
                    'food_category.create',
                    'food_category.view',
                    'food_category.edit',
                    'food_category.delete',
                ]
            ],
            [
                'group_name' => 'material_category',
                'permissions' => [
                    // material_category Permissions
                    'material_category.create',
                    'material_category.view',
                    'material_category.edit',
                    'material_category.delete',
                ]
            ],


            [
                'group_name' => 'employe',
                'permissions' => [
                    // employe Permissions
                    'employe.create',
                    'employe.view',
                    'employe.edit',
                    'employe.delete',
                ]
            ],
            [
                'group_name' => 'position',
                'permissions' => [
                    // position Permissions
                    'position.create',
                    'position.view',
                    'position.edit',
                    'position.delete',
                ]
            ],
            [
                'group_name' => 'barrist_production_store',
                'permissions' => [
                    // barrist_production_store Permissions
                    'barrist_production_store.view',
                    'barrist_production_store.edit',
                    'barrist_production_store.delete',
                ]
            ],
            [
                'group_name' => 'bartender_production_store',
                'permissions' => [
                    // bartender_production_store Permissions
                    'bartender_production_store.view',
                    'bartender_production_store.edit',
                    'bartender_production_store.delete',
                ]
            ],
            [
                'group_name' => 'drink_extra_big_store',
                'permissions' => [
                    // drink_extra_big_store Permissions
                    'drink_extra_big_store.create',
                    'drink_extra_big_store.view',
                    'drink_extra_big_store.edit',
                    'drink_extra_big_store.delete',
                ]
            ],
            [
                'group_name' => 'drink_big_store',
                'permissions' => [
                    // drink_big_store Permissions
                    'drink_big_store.create',
                    'drink_big_store.view',
                    'drink_big_store.edit',
                    'drink_big_store.delete',
                ]
            ],
            [
                'group_name' => 'drink_small_store',
                'permissions' => [
                    // drink_small_store Permissions
                    'drink_small_store.create',
                    'drink_small_store.view',
                    'drink_small_store.edit',
                    'drink_small_store.delete',
                ]
            ],
            [
                'group_name' => 'food_store',
                'permissions' => [
                    // food_store Permissions
                    'food_store.create',
                    'food_store.view',
                    'food_store.edit',
                    'food_store.delete'
                ]
            ],
            [
                'group_name' => 'food_big_store',
                'permissions' => [
                    // food_big_store Permissions
                    'food_big_store.create',
                    'food_big_store.view',
                    'food_big_store.edit',
                    'food_big_store.delete'
                ]
            ],
            [
                'group_name' => 'food_extra_big_store',
                'permissions' => [
                    // food_extra_big_store Permissions
                    'food_extra_big_store.create',
                    'food_extra_big_store.view',
                    'food_extra_big_store.edit',
                    'food_extra_big_store.delete'
                ]
            ],
            [
                'group_name' => 'food_small_store',
                'permissions' => [
                    // food_small_store Permissions
                    'food_small_store.create',
                    'food_small_store.view',
                    'food_small_store.edit',
                    'food_small_store.delete'
                ]
            ],
            [
                'group_name' => 'material_big_store',
                'permissions' => [
                    // material_big_store Permissions
                    'material_big_store.create',
                    'material_big_store.view',
                    'material_big_store.edit',
                    'material_big_store.delete',
                ]
            ],
            [
                'group_name' => 'material_small_store',
                'permissions' => [
                    // material_small_store Permissions
                    'material_small_store.create',
                    'material_small_store.view',
                    'material_small_store.edit',
                    'material_small_store.delete',
                ]
            ],
            [
                'group_name' => 'material_extra_big_store',
                'permissions' => [
                    // material_extra_big_store Permissions
                    'material_extra_big_store.create',
                    'material_extra_big_store.view',
                    'material_extra_big_store.edit',
                    'material_extra_big_store.delete',
                ]
            ],
            [
                'group_name' => 'drink_reception',
                'permissions' => [
                    // drink_reception Permissions
                    'drink_reception.create',
                    'drink_reception.view',
                    'drink_reception.edit',
                    'drink_reception.show',
                    'drink_reception.delete',
                    'drink_reception.validate',
                    'drink_reception.confirm',
                    'drink_reception.approuve',
                    'drink_reception.reset',
                    'drink_reception.reject',
                ]
            ],
            [
                'group_name' => 'food_reception',
                'permissions' => [
                    // food_reception Permissions
                    'food_reception.create',
                    'food_reception.view',
                    'food_reception.edit',
                    'food_reception.show',
                    'food_reception.delete',
                    'food_reception.validate',
                    'food_reception.confirm',
                    'food_reception.approuve',
                    'food_reception.reset',
                    'food_reception.reject',
                ]
            ],
            [
                'group_name' => 'material_reception',
                'permissions' => [
                    // material_reception Permissions
                    'material_reception.create',
                    'material_reception.view',
                    'material_reception.edit',
                    'material_reception.show',
                    'material_reception.delete',
                    'material_reception.validate',
                    'material_reception.confirm',
                    'material_reception.approuve',
                    'material_reception.reset',
                    'material_reception.reject',
                ]
            ],
            [
                'group_name' => 'drink_requisition',
                'permissions' => [
                    // drink_requisition Permissions
                    'drink_requisition.create',
                    'drink_requisition.view',
                    'drink_requisition.edit',
                    'drink_requisition.show',
                    'drink_requisition.delete',
                    'drink_requisition.validate',
                    'drink_requisition.confirm',
                    'drink_requisition.approuve',
                    'drink_requisition.reset',
                    'drink_requisition.reject',
                ]
            ],
            [
                'group_name' => 'food_requisition',
                'permissions' => [
                    // food_requisition Permissions
                    'food_requisition.create',
                    'food_requisition.view',
                    'food_requisition.edit',
                    'food_requisition.show',
                    'food_requisition.delete',
                    'food_requisition.validate',
                    'food_requisition.confirm',
                    'food_requisition.approuve',
                    'food_requisition.reset',
                    'food_requisition.reject',
                ]
            ],
            [
                'group_name' => 'material_requisition',
                'permissions' => [
                    // material_requisition Permissions
                    'material_requisition.create',
                    'material_requisition.view',
                    'material_requisition.edit',
                    'material_requisition.show',
                    'material_requisition.delete',
                    'material_requisition.validate',
                    'material_requisition.confirm',
                    'material_requisition.approuve',
                    'material_requisition.reset',
                    'material_requisition.reject',
                ]
            ],
            [
                'group_name' => 'barrist_requisition',
                'permissions' => [
                    // barrist_requisition Permissions
                    'barrist_requisition.create',
                    'barrist_requisition.view',
                    'barrist_requisition.edit',
                    'barrist_requisition.show',
                    'barrist_requisition.delete',
                    'barrist_requisition.validate',
                    'barrist_requisition.confirm',
                    'barrist_requisition.approuve',
                    'barrist_requisition.reset',
                    'barrist_requisition.reject',
                ]
            ],
            [
                'group_name' => 'food_order_client',
                'permissions' => [
                    // food_order_client Permissions
                    'food_order_client.create',
                    'food_order_client.view',
                    'food_order_client.edit',
                    'food_order_client.show',
                    'food_order_client.delete',
                    'food_order_client.validate',
                    'food_order_client.confirm',
                    'food_order_client.approuve',
                    'food_order_client.reset',
                    'food_order_client.reject',
                ]
            ],
            [
                'group_name' => 'drink_order_client',
                'permissions' => [
                    // drink_order_client Permissions
                    'drink_order_client.create',
                    'drink_order_client.view',
                    'drink_order_client.edit',
                    'drink_order_client.show',
                    'drink_order_client.delete',
                    'drink_order_client.validate',
                    'drink_order_client.confirm',
                    'drink_order_client.approuve',
                    'drink_order_client.reset',
                    'drink_order_client.reject',
                ]
            ],
            [
                'group_name' => 'drink_transfer',
                'permissions' => [
                    // drink_transfer Permissions
                    'drink_transfer.create',
                    'drink_transfer.view',
                    'drink_transfer.edit',
                    'drink_transfer.show',
                    'drink_transfer.delete',
                    'drink_transfer.validate',
                    'drink_transfer.confirm',
                    'drink_transfer.approuve',
                    'drink_transfer.reset',
                    'drink_transfer.reject',
                ]
            ],
            [
                'group_name' => 'food_transfer',
                'permissions' => [
                    // food_transfer Permissions
                    'food_transfer.create',
                    'food_transfer.view',
                    'food_transfer.edit',
                    'food_transfer.show',
                    'food_transfer.delete',
                    'food_transfer.validate',
                    'food_transfer.confirm',
                    'food_transfer.approuve',
                    'food_transfer.reset',
                    'food_transfer.reject',
                    'food_transfer.validatePortion',
                    'food_transfer.portion',
                ]
            ],
            [
                'group_name' => 'material_transfer',
                'permissions' => [
                    // material_transfer Permissions
                    'material_transfer.create',
                    'material_transfer.view',
                    'material_transfer.edit',
                    'material_transfer.show',
                    'material_transfer.delete',
                    'material_transfer.validate',
                    'material_transfer.confirm',
                    'material_transfer.approuve',
                    'material_transfer.reset',
                    'material_transfer.reject',
                ]
            ],

            [
                'group_name' => 'barrist_transfer',
                'permissions' => [
                    // barrist_transfer Permissions
                    'barrist_transfer.create',
                    'barrist_transfer.view',
                    'barrist_transfer.edit',
                    'barrist_transfer.show',
                    'barrist_transfer.delete',
                    'barrist_transfer.validate',
                    'barrist_transfer.confirm',
                    'barrist_transfer.approuve',
                    'barrist_transfer.reset',
                    'barrist_transfer.reject',
                ]
            ],
            [
                'group_name' => 'drink_return',
                'permissions' => [
                    // drink_return Permissions
                    'drink_return.create',
                    'drink_return.view',
                    'drink_return.edit',
                    'drink_return.show',
                    'drink_return.delete',
                    'drink_return.validate',
                    'drink_return.confirm',
                    'drink_return.approuve',
                    'drink_return.reset',
                    'drink_return.reject',
                ]
            ],
            [
                'group_name' => 'food_return',
                'permissions' => [
                    // food_return Permissions
                    'food_return.create',
                    'food_return.view',
                    'food_return.edit',
                    'food_return.show',
                    'food_return.delete',
                    'food_return.validate',
                    'food_return.confirm',
                    'food_return.approuve',
                    'food_return.reset',
                    'food_return.reject',
                ]
            ],
            [
                'group_name' => 'material_return',
                'permissions' => [
                    // material_return Permissions
                    'material_return.create',
                    'material_return.view',
                    'material_return.edit',
                    'material_return.show',
                    'material_return.delete',
                    'material_return.validate',
                    'material_return.confirm',
                    'material_return.approuve',
                    'material_return.reset',
                    'material_return.reject',
                ]
            ],
            [
                'group_name' => 'drink_purchase',
                'permissions' => [
                    // drink_purchase Permissions
                    'drink_purchase.create',
                    'drink_purchase.view',
                    'drink_purchase.edit',
                    'drink_purchase.show',
                    'drink_purchase.delete',
                    'drink_purchase.validate',
                    'drink_purchase.confirm',
                    'drink_purchase.approuve',
                    'drink_purchase.reset',
                    'drink_purchase.reject',
                ]
            ],

            [
                'group_name' => 'food_purchase',
                'permissions' => [
                    // food_purchase Permissions
                    'food_purchase.create',
                    'food_purchase.view',
                    'food_purchase.edit',
                    'food_purchase.show',
                    'food_purchase.delete',
                    'food_purchase.validate',
                    'food_purchase.confirm',
                    'food_purchase.approuve',
                    'food_purchase.reset',
                    'food_purchase.reject',
                ]
            ],

            [
                'group_name' => 'material_purchase',
                'permissions' => [
                    // material_purchase Permissions
                    'material_purchase.create',
                    'material_purchase.view',
                    'material_purchase.edit',
                    'material_purchase.show',
                    'material_purchase.delete',
                    'material_purchase.validate',
                    'material_purchase.confirm',
                    'material_purchase.approuve',
                    'material_purchase.reset',
                    'material_purchase.reject',
                ]
            ],

            [
                'group_name' => 'food_stockin',
                'permissions' => [
                    // food_stockin Permissions
                    'food_stockin.create',
                    'food_stockin.view',
                    'food_stockin.edit',
                    'food_stockin.show',
                    'food_stockin.delete',
                    'food_stockin.validate',
                    'food_stockin.confirm',
                    'food_stockin.approuve',
                    'food_stockin.reset',
                    'food_stockin.reject',
                ]
            ],
            [
                'group_name' => 'drink_stockin',
                'permissions' => [
                    // drink_stockin Permissions
                    'drink_stockin.create',
                    'drink_stockin.view',
                    'drink_stockin.edit',
                    'drink_stockin.show',
                    'drink_stockin.delete',
                    'drink_stockin.validate',
                    'drink_stockin.confirm',
                    'drink_stockin.approuve',
                    'drink_stockin.reset',
                    'drink_stockin.reject',
                ]
            ],
            [
                'group_name' => 'material_stockin',
                'permissions' => [
                    // material_stockin Permissions
                    'material_stockin.create',
                    'material_stockin.view',
                    'material_stockin.edit',
                    'material_stockin.show',
                    'material_stockin.delete',
                    'material_stockin.validate',
                    'material_stockin.confirm',
                    'material_stockin.approuve',
                    'material_stockin.reset',
                    'material_stockin.reject',
                ]
            ],
            [
                'group_name' => 'drink_stockout',
                'permissions' => [
                    // drink_stockout Permissions
                    'drink_stockout.create',
                    'drink_stockout.view',
                    'drink_stockout.edit',
                    'drink_stockout.show',
                    'drink_stockout.delete',
                    'drink_stockout.validate',
                    'drink_stockout.confirm',
                    'drink_stockout.approuve',
                    'drink_stockout.reset',
                    'drink_stockout.reject',
                ]
            ],
            [
                'group_name' => 'material_stockout',
                'permissions' => [
                    // material_stockout Permissions
                    'material_stockout.create',
                    'material_stockout.view',
                    'material_stockout.edit',
                    'material_stockout.show',
                    'material_stockout.delete',
                    'material_stockout.validate',
                    'material_stockout.confirm',
                    'material_stockout.approuve',
                    'material_stockout.reset',
                    'material_stockout.reject',
                ]
            ],
            [
                'group_name' => 'food_stockout',
                'permissions' => [
                    // food_stockout Permissions
                    'food_stockout.create',
                    'food_stockout.view',
                    'food_stockout.edit',
                    'food_stockout.show',
                    'food_stockout.delete',
                    'food_stockout.validate',
                    'food_stockout.confirm',
                    'food_stockout.approuve',
                    'food_stockout.reset',
                    'food_stockout.reject'
                ]
            ],

            [
                'group_name' => 'supplier',
                'permissions' => [
                    // supplier Permissions
                    'supplier.create',
                    'supplier.view',
                    'supplier.edit',
                    'supplier.delete',
                ]
            ],
            [
                'group_name' => 'client',
                'permissions' => [
                    // client Permissions
                    'client.create',
                    'client.view',
                    'client.edit',
                    'client.delete',
                ]
            ],
            [
                'group_name' => 'table',
                'permissions' => [
                    // table Permissions
                    'table.create',
                    'table.view',
                    'table.edit',
                    'table.delete',
                ]
            ],
             [
                'group_name' => 'setting',
                'permissions' => [
                    // setting Permissions
                    'setting.create',
                    'setting.view',
                    'setting.edit',
                    'setting.delete',
                ]
            ],
            [
                'group_name' => 'drink_supplier_order',
                'permissions' => [
                    // drink_supplier_order Permissions
                    'drink_supplier_order.create',
                    'drink_supplier_order.view',
                    'drink_supplier_order.edit',
                    'drink_supplier_order.delete',
                    'drink_supplier_order.validate',
                    'drink_supplier_order.confirm',
                    'drink_supplier_order.approuve',
                    'drink_supplier_order.reset',
                    'drink_supplier_order.reject',
                ]
            ],
            [
                'group_name' => 'food_supplier_order',
                'permissions' => [
                    // food_supplier_order Permissions
                    'food_supplier_order.create',
                    'food_supplier_order.view',
                    'food_supplier_order.edit',
                    'food_supplier_order.delete',
                    'food_supplier_order.validate',
                    'food_supplier_order.confirm',
                    'food_supplier_order.approuve',
                    'food_supplier_order.reset',
                    'food_supplier_order.reject',
                ]
            ],
            [
                'group_name' => 'material_supplier_order',
                'permissions' => [
                    // material_supplier_order Permissions
                    'material_supplier_order.create',
                    'material_supplier_order.view',
                    'material_supplier_order.edit',
                    'material_supplier_order.delete',
                    'material_supplier_order.validate',
                    'material_supplier_order.confirm',
                    'material_supplier_order.approuve',
                    'material_supplier_order.reset',
                    'material_supplier_order.reject',
                ]
            ],
            [
                'group_name' => 'fiches',
                'permissions' => [
                    // fiches Permissions
                    'bon_entree.imprimer',
                    'bon_sortie.imprimer',
                    'fiche_reception_boisson.imprimer',
                    'fiche_reception_nourriture.imprimer',
                    'fiche_reception_material.imprimer',
                    'facture.imprimer',
                    'fiche_commande_boisson.imprimer',
                    'fiche_commande_nourriture.imprimer',
                    'fiche_commande_materiel.imprimer',
                    'fiche_requisition_boisson.imprimer',
                    'fiche_requisition_nourriture.imprimer',
                    'fiche_requisition_materiel.imprimer',
                    'fiche_transfert_boisson.imprimer',
                    'fiche_transfert_nourriture.imprimer',
                    'fiche_transfert_materiel.imprimer',
                    'fiche_stock_boisson.imprimer',
                    'fiche_stock_nourriture.imprimer',
                    'fiche_stock_materiel.imprimer',
                    'fiche_rapport_boisson.imprimer',
                    'fiche_rapport_nourriture.imprimer',
                    'fiche_rapport_materiel.imprimer',
                ]
            ],
            [
                'group_name' => 'drink_extra_big_inventory',
                'permissions' => [
                    // drink_extra_big_inventory Permissions
                    'drink_extra_big_inventory.view',
                    'drink_extra_big_inventory.create',
                    'drink_extra_big_inventory.edit',
                    'drink_extra_big_inventory.show',
                    'drink_extra_big_inventory.delete',
                    'drink_extra_big_inventory.validate',
                    'drink_extra_big_inventory.reset',
                    'drink_extra_big_inventory.reject',
                ]
            ],
            [
                'group_name' => 'drink_big_inventory',
                'permissions' => [
                    // drink_big_inventory Permissions
                    'drink_big_inventory.view',
                    'drink_big_inventory.create',
                    'drink_big_inventory.edit',
                    'drink_big_inventory.show',
                    'drink_big_inventory.delete',
                    'drink_big_inventory.validate',
                    'drink_big_inventory.reset',
                    'drink_big_inventory.reject',
                ]
            ],
            [
                'group_name' => 'drink_small_inventory',
                'permissions' => [
                    // drink_small_inventory Permissions
                    'drink_small_inventory.view',
                    'drink_small_inventory.create',
                    'drink_small_inventory.edit',
                    'drink_small_inventory.show',
                    'drink_small_inventory.delete',
                    'drink_small_inventory.validate',
                    'drink_small_inventory.reset',
                    'drink_small_inventory.reject',
                ]
            ],
            [
                'group_name' => 'food_extra_big_inventory',
                'permissions' => [
                    // food_extra_big_inventory Permissions
                    'food_extra_big_inventory.view',
                    'food_extra_big_inventory.create',
                    'food_extra_big_inventory.edit',
                    'food_extra_big_inventory.show',
                    'food_extra_big_inventory.delete',
                    'food_extra_big_inventory.validate',
                    'food_extra_big_inventory.reset',
                    'food_extra_big_inventory.reject',
                ]
            ],
            [
                'group_name' => 'food_big_inventory',
                'permissions' => [
                    // food_big_inventory Permissions
                    'food_big_inventory.view',
                    'food_big_inventory.create',
                    'food_big_inventory.edit',
                    'food_big_inventory.show',
                    'food_big_inventory.delete',
                    'food_big_inventory.validate',
                    'food_big_inventory.reset',
                    'food_big_inventory.reject',
                ]
            ],
            [
                'group_name' => 'food_small_inventory',
                'permissions' => [
                    // food_small_inventory Permissions
                    'food_small_inventory.view',
                    'food_small_inventory.create',
                    'food_small_inventory.edit',
                    'food_small_inventory.show',
                    'food_small_inventory.delete',
                    'food_small_inventory.validate',
                    'food_small_inventory.reset',
                    'food_small_inventory.reject',
                ]
            ],
            [
                'group_name' => 'material_extra_big_inventory',
                'permissions' => [
                    // material_extra_big_inventory Permissions
                    'material_extra_big_inventory.view',
                    'material_extra_big_inventory.create',
                    'material_extra_big_inventory.edit',
                    'material_extra_big_inventory.show',
                    'material_extra_big_inventory.delete',
                    'material_extra_big_inventory.validate',
                    'material_extra_big_inventory.reset',
                    'material_extra_big_inventory.reject',
                ]
            ],
            [
                'group_name' => 'material_big_inventory',
                'permissions' => [
                    // material_big_inventory Permissions
                    'material_big_inventory.view',
                    'material_big_inventory.create',
                    'material_big_inventory.edit',
                    'material_big_inventory.show',
                    'material_big_inventory.delete',
                    'material_big_inventory.validate',
                    'material_big_inventory.reset',
                    'material_big_inventory.reject',
                ]
            ],
            [
                'group_name' => 'material_small_inventory',
                'permissions' => [
                    // material_small_inventory Permissions
                    'material_small_inventory.view',
                    'material_small_inventory.create',
                    'material_small_inventory.edit',
                    'material_small_inventory.show',
                    'material_small_inventory.delete',
                    'material_small_inventory.validate',
                    'material_small_inventory.reset',
                    'material_small_inventory.reject',
                ]
            ],
            [
                'group_name' => 'invoice_drink',
                'permissions' => [
                    // invoice_drink Permissions
                    'invoice_drink.view',
                    'invoice_drink.create',
                    'invoice_drink.edit',
                    'invoice_drink.show',
                    'invoice_drink.delete',
                    'invoice_drink.validate',
                    'invoice_drink.reset',
                    'invoice_drink.reject',
                ]
            ],
            [
                'group_name' => 'invoice_kitchen',
                'permissions' => [
                    // invoice_kitchen Permissions
                    'invoice_kitchen.view',
                    'invoice_kitchen.create',
                    'invoice_kitchen.edit',
                    'invoice_kitchen.show',
                    'invoice_kitchen.delete',
                    'invoice_kitchen.validate',
                    'invoice_kitchen.reset',
                    'invoice_kitchen.reject',
                ]
            ],
            [
                'group_name' => 'invoice_obr',
                'permissions' => [
                    // invoice_obr Permissions
                    'invoice_obr.view',
                    'invoice_obr.send',
                    'invoice_obr.reset',
                ]
            ],
            [
                'group_name' => 'barrist_report',
                'permissions' => [
                    // barrist_report Permissions
                    'barrist_report.view',
                    'barrist_report.edit',
                ]
            ],
            [
                'group_name' => 'drink_extra_big_report',
                'permissions' => [
                    // drink_extra_big_report Permissions
                    'drink_extra_big_report.view',
                    'drink_extra_big_report.edit',
                ]
            ],
            [
                'group_name' => 'drink_big_report',
                'permissions' => [
                    // drink_big_report Permissions
                    'drink_big_report.view',
                    'drink_big_report.edit',
                ]
            ],
            [
                'group_name' => 'drink_small_report',
                'permissions' => [
                    // drink_small_report Permissions
                    'drink_small_report.view',
                    'drink_small_report.edit',
                ]
            ],
            [
                'group_name' => 'food_extra_big_report',
                'permissions' => [
                    // food_extra_big_report Permissions
                    'food_extra_big_report.view',
                    'food_extra_big_report.edit',
                ]
            ],
            [
                'group_name' => 'food_big_report',
                'permissions' => [
                    // food_big_report Permissions
                    'food_big_report.view',
                    'food_big_report.edit',
                ]
            ],
            [
                'group_name' => 'food_small_report',
                'permissions' => [
                    // food_small_report Permissions
                    'food_small_report.view',
                    'food_small_report.edit',
                ]
            ],
            [
                'group_name' => 'material_extra_big_report',
                'permissions' => [
                    // material_extra_big_report Permissions
                    'material_extra_big_report.view',
                    'material_extra_big_report.edit',
                ]
            ],
            [
                'group_name' => 'material_big_report',
                'permissions' => [
                    // material_big_report Permissions
                    'material_big_report.view',
                    'material_big_report.edit',
                ]
            ],
            [
                'group_name' => 'material_small_report',
                'permissions' => [
                    // material_small_report Permissions
                    'material_small_report.view',
                    'material_small_report.edit',
                ]
            ],
            [
                'group_name' => 'food_store_report',
                'permissions' => [
                    // food_store_report Permissions
                    'food_store_report.view',
                    'food_store_report.edit',
                ]
            ],
            [
                'group_name' => 'hr_departement',
                'permissions' => [
                    // hr_departement Permissions
                    'hr_departement.create',
                    'hr_departement.view',
                    'hr_departement.edit',
                    'hr_departement.delete',
                ]
            ],
            [
                'group_name' => 'hr_service',
                'permissions' => [
                    // hr_service Permissions
                    'hr_service.create',
                    'hr_service.view',
                    'hr_service.edit',
                    'hr_service.delete',
                ]
            ],
            [
                'group_name' => 'hr_fonction',
                'permissions' => [
                    // hr_fonction Permissions
                    'hr_fonction.create',
                    'hr_fonction.view',
                    'hr_fonction.edit',
                    'hr_fonction.delete',
                ]
            ],

            [
                'group_name' => 'hr_grade',
                'permissions' => [
                    // hr_grade Permissions
                    'hr_grade.create',
                    'hr_grade.view',
                    'hr_grade.edit',
                    'hr_grade.delete',
                ]
            ],
            [
                'group_name' => 'hr_employe',
                'permissions' => [
                    // hr_employe Permissions
                    'hr_employe.create',
                    'hr_employe.view',
                    'hr_employe.edit',
                    'hr_employe.delete',
                ]
            ],
            [
                'group_name' => 'hr_banque',
                'permissions' => [
                    // hr_banque Permissions
                    'hr_banque.create',
                    'hr_banque.view',
                    'hr_banque.edit',
                    'hr_banque.delete',
                ]
            ],
            [
                'group_name' => 'hr_stagiaire',
                'permissions' => [
                    // hr_stagiaire Permissions
                    'hr_stagiaire.create',
                    'hr_stagiaire.view',
                    'hr_stagiaire.edit',
                    'hr_stagiaire.delete',
                ]
            ],
            [
                'group_name' => 'hr_ecole',
                'permissions' => [
                    // hr_ecole Permissions
                    'hr_ecole.create',
                    'hr_ecole.view',
                    'hr_ecole.edit',
                    'hr_ecole.delete',
                ]
            ],
            [
                'group_name' => 'hr_filiere',
                'permissions' => [
                    // hr_filiere Permissions
                    'hr_filiere.create',
                    'hr_filiere.view',
                    'hr_filiere.edit',
                    'hr_filiere.delete',
                ]
            ],
            [
                'group_name' => 'hr_cotation',
                'permissions' => [
                    // hr_cotation Permissions
                    'hr_cotation.create',
                    'hr_cotation.view',
                    'hr_cotation.edit',
                    'hr_cotation.delete',
                ]
            ],
            [
                'group_name' => 'hr_cotisation',
                'permissions' => [
                    // hr_cotisation Permissions
                    'hr_cotisation.create',
                    'hr_cotisation.view',
                    'hr_cotisation.edit',
                    'hr_cotisation.delete',
                ]
            ],
            [
                'group_name' => 'hr_indemnite',
                'permissions' => [
                    // hr_indemnite Permissions
                    'hr_indemnite.create',
                    'hr_indemnite.view',
                    'hr_indemnite.edit',
                    'hr_indemnite.delete',
                ]
            ],
            [
                'group_name' => 'hr_conge',
                'permissions' => [
                    // hr_conge Permissions
                    'hr_conge.create',
                    'hr_conge.view',
                    'hr_conge.edit',
                    'hr_conge.delete',
                ]
            ],
            [
                'group_name' => 'hr_conge_paye',
                'permissions' => [
                    // hr_conge_paye Permissions
                    'hr_conge_paye.create',
                    'hr_conge_paye.view',
                    'hr_conge_paye.edit',
                    'hr_conge_paye.delete',
                ]
            ],
            [
                'group_name' => 'hr_reglage',
                'permissions' => [
                    // hr_reglage Permissions
                    'hr_reglage.create',
                    'hr_reglage.view',
                    'hr_reglage.edit',
                    'hr_reglage.delete',
                ]
            ],
            [
                'group_name' => 'hr_note_interne',
                'permissions' => [
                    // hr_note_interne Permissions
                    'hr_note_interne.create',
                    'hr_note_interne.view',
                    'hr_note_interne.edit',
                    'hr_note_interne.delete',
                ]
            ],
            [
                'group_name' => 'hr_impot',
                'permissions' => [
                    // hr_impot Permissions
                    'hr_impot.create',
                    'hr_impot.view',
                    'hr_impot.edit',
                    'hr_impot.delete',
                ]
            ],
            [
                'group_name' => 'hr_prime',
                'permissions' => [
                    // hr_prime Permissions
                    'hr_prime.create',
                    'hr_prime.view',
                    'hr_prime.edit',
                    'hr_prime.delete',
                ]
            ],
            [
                'group_name' => 'hr_paiement',
                'permissions' => [
                    // hr_paiement Permissions
                    'hr_paiement.create',
                    'hr_paiement.view',
                    'hr_paiement.show',
                    'hr_paiement.edit',
                    'hr_paiement.print',
                    'hr_paiement.delete',
                ]
            ],
            [
                'group_name' => 'hr_journal_paie',
                'permissions' => [
                    // hr_journal_paie Permissions
                    'hr_journal_paie.create',
                    'hr_journal_paie.view',
                    'hr_journal_paie.cloturer',
                    'hr_journal_paie.show',
                    'hr_journal_paie.edit',
                    'hr_journal_paie.delete',
                ]
            ],
            [
                'group_name' => 'hr_journal_conge',
                'permissions' => [
                    // hr_journal_conge Permissions
                    'hr_journal_conge.create',
                    'hr_journal_conge.view',
                    'hr_journal_conge.show',
                    'hr_journal_conge.edit',
                    'hr_journal_conge.delete',
                ]
            ],
            [
                'group_name' => 'hr_journal_cotisation',
                'permissions' => [
                    // hr_journal_cotisation Permissions
                    'hr_journal_cotisation.create',
                    'hr_journal_cotisation.view',
                    'hr_journal_cotisation.show',
                    'hr_journal_cotisation.edit',
                    'hr_journal_cotisation.delete',
                ]
            ],
            [
                'group_name' => 'hr_absence',
                'permissions' => [
                    // hr_absence Permissions
                    'hr_absence.create',
                    'hr_absence.view',
                    'hr_absence.show',
                    'hr_absence.edit',
                    'hr_absence.delete',
                ]
            ],
            [
                'group_name' => 'hr_loan',
                'permissions' => [
                    // hr_loan Permissions
                    'hr_loan.create',
                    'hr_loan.view',
                    'hr_loan.show',
                    'hr_loan.edit',
                    'hr_loan.delete',
                ]
            ],
            [
                'group_name' => 'booking_service',
                'permissions' => [
                    // booking_service Permissions
                    'booking_service.create',
                    'booking_service.view',
                    'booking_service.show',
                    'booking_service.edit',
                    'booking_service.delete',
                ]
            ],
            [
                'group_name' => 'booking_client',
                'permissions' => [
                    // booking_client Permissions
                    'booking_client.create',
                    'booking_client.view',
                    'booking_client.show',
                    'booking_client.edit',
                    'booking_client.delete',
                ]
            ],
            [
                'group_name' => 'booking_technique',
                'permissions' => [
                    // booking_technique Permissions
                    'booking_technique.create',
                    'booking_technique.view',
                    'booking_technique.show',
                    'booking_technique.edit',
                    'booking_technique.delete',
                ]
            ],
            [
                'group_name' => 'booking_salle',
                'permissions' => [
                    // booking_salle Permissions
                    'booking_salle.create',
                    'booking_salle.view',
                    'booking_salle.show',
                    'booking_salle.edit',
                    'booking_salle.delete',
                ]
            ],
            [
                'group_name' => 'booking',
                'permissions' => [
                    // booking Permissions
                    'booking.create',
                    'booking.view',
                    'booking.show',
                    'booking.validate',
                    'booking.reject',
                    'booking.reset',
                    'booking.confirm',
                    'booking.approuve',
                    'booking.edit',
                    'booking.delete',
                ]
            ],
            [
                'group_name' => 'invoice_booking',
                'permissions' => [
                    // invoice_booking Permissions
                    'invoice_booking.create',
                    'invoice_booking.view',
                    'invoice_booking.show',
                    'invoice_booking.validate',
                    'invoice_booking.reject',
                    'invoice_booking.reset',
                    'invoice_booking.confirm',
                    'invoice_booking.approuve',
                    'invoice_booking.edit',
                    'invoice_booking.delete',
                ]
            ],
            [
                'group_name' => 'profile',
                'permissions' => [
                    // profile Permissions
                    'profile.view',
                    'profile.edit',
                ]
            ],
            [
                'group_name' => 'sotb_dashboard',
                'permissions' => [
                    // sotb_dashboard Permissions
                    'sotb_dashboard.view',
                    'sotb_dashboard.edit',
                ]
            ],
            [
                'group_name' => 'musumba_steel_dashboard',
                'permissions' => [
                    // musumba_steel_dashboard Permissions
                    'musumba_steel_dashboard.view',
                    'musumba_steel_dashboard.edit',
                ]
            ],
            [
                'group_name' => 'musumba_holding_data',
                'permissions' => [
                    // musumba_holding_data Permissions
                    'musumba_holding_data.view',
                    'musumba_holding_data.edit',
                ]
            ],

            [
                'group_name' => 'sotb_material',
                'permissions' => [
                    // sotb_material Permissions
                    'sotb_material.create',
                    'sotb_material.view',
                    'sotb_material.edit',
                    'sotb_material.delete',
                ]
            ],
            [
                'group_name' => 'sotb_material_category',
                'permissions' => [
                    // sotb_material_category Permissions
                    'sotb_material_category.create',
                    'sotb_material_category.view',
                    'sotb_material_category.edit',
                    'sotb_material_category.delete',
                ]
            ],
            [
                'group_name' => 'sotb_material_bg_store',
                'permissions' => [
                    // sotb_material_bg_store Permissions
                    'sotb_material_bg_store.create',
                    'sotb_material_bg_store.view',
                    'sotb_material_bg_store.edit',
                    'sotb_material_bg_store.delete',
                ]
            ],
            [
                'group_name' => 'sotb_material_sm_store',
                'permissions' => [
                    // sotb_material_sm_store Permissions
                    'sotb_material_sm_store.create',
                    'sotb_material_sm_store.view',
                    'sotb_material_sm_store.edit',
                    'sotb_material_sm_store.delete',
                ]
            ],
            [
                'group_name' => 'sotb_material_md_store',
                'permissions' => [
                    // sotb_material_md_store Permissions
                    'sotb_material_md_store.create',
                    'sotb_material_md_store.view',
                    'sotb_material_md_store.edit',
                    'sotb_material_md_store.delete',
                ]
            ],
            [
                'group_name' => 'sotb_material_reception',
                'permissions' => [
                    // sotb_material_reception Permissions
                    'sotb_material_reception.create',
                    'sotb_material_reception.view',
                    'sotb_material_reception.edit',
                    'sotb_material_reception.show',
                    'sotb_material_reception.delete',
                    'sotb_material_reception.validate',
                    'sotb_material_reception.confirm',
                    'sotb_material_reception.approuve',
                    'sotb_material_reception.reset',
                    'sotb_material_reception.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_requisition',
                'permissions' => [
                    // sotb_material_requisition Permissions
                    'sotb_material_requisition.create',
                    'sotb_material_requisition.view',
                    'sotb_material_requisition.edit',
                    'sotb_material_requisition.show',
                    'sotb_material_requisition.delete',
                    'sotb_material_requisition.validate',
                    'sotb_material_requisition.confirm',
                    'sotb_material_requisition.approuve',
                    'sotb_material_requisition.reset',
                    'sotb_material_requisition.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_transfert',
                'permissions' => [
                    // sotb_material_transfert Permissions
                    'sotb_material_transfert.create',
                    'sotb_material_transfert.view',
                    'sotb_material_transfert.edit',
                    'sotb_material_transfert.show',
                    'sotb_material_transfert.delete',
                    'sotb_material_transfert.validate',
                    'sotb_material_transfert.confirm',
                    'sotb_material_transfert.approuve',
                    'sotb_material_transfert.reset',
                    'sotb_material_transfert.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_return',
                'permissions' => [
                    // sotb_material_return Permissions
                    'sotb_material_return.create',
                    'sotb_material_return.view',
                    'sotb_material_return.edit',
                    'sotb_material_return.show',
                    'sotb_material_return.delete',
                    'sotb_material_return.validate',
                    'sotb_material_return.confirm',
                    'sotb_material_return.approuve',
                    'sotb_material_return.reset',
                    'sotb_material_return.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_purchase',
                'permissions' => [
                    // sotb_material_purchase Permissions
                    'sotb_material_purchase.create',
                    'sotb_material_purchase.view',
                    'sotb_material_purchase.edit',
                    'sotb_material_purchase.show',
                    'sotb_material_purchase.delete',
                    'sotb_material_purchase.validate',
                    'sotb_material_purchase.confirm',
                    'sotb_material_purchase.approuve',
                    'sotb_material_purchase.reset',
                    'sotb_material_purchase.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_stockin',
                'permissions' => [
                    // sotb_material_stockin Permissions
                    'sotb_material_stockin.create',
                    'sotb_material_stockin.view',
                    'sotb_material_stockin.edit',
                    'sotb_material_stockin.show',
                    'sotb_material_stockin.delete',
                    'sotb_material_stockin.validate',
                    'sotb_material_stockin.confirm',
                    'sotb_material_stockin.approuve',
                    'sotb_material_stockin.reset',
                    'sotb_material_stockin.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_stockout',
                'permissions' => [
                    // sotb_material_stockout Permissions
                    'sotb_material_stockout.create',
                    'sotb_material_stockout.view',
                    'sotb_material_stockout.edit',
                    'sotb_material_stockout.show',
                    'sotb_material_stockout.delete',
                    'sotb_material_stockout.validate',
                    'sotb_material_stockout.confirm',
                    'sotb_material_stockout.approuve',
                    'sotb_material_stockout.reset',
                    'sotb_material_stockout.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_supplier_order',
                'permissions' => [
                    // sotb_material_supplier_order Permissions
                    'sotb_material_supplier_order.create',
                    'sotb_material_supplier_order.view',
                    'sotb_material_supplier_order.edit',
                    'sotb_material_supplier_order.delete',
                    'sotb_material_supplier_order.validate',
                    'sotb_material_supplier_order.confirm',
                    'sotb_material_supplier_order.approuve',
                    'sotb_material_supplier_order.reset',
                    'sotb_material_supplier_order.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_bg_inventory',
                'permissions' => [
                    // sotb_material_bg_inventory Permissions
                    'sotb_material_bg_inventory.view',
                    'sotb_material_bg_inventory.create',
                    'sotb_material_bg_inventory.edit',
                    'sotb_material_bg_inventory.show',
                    'sotb_material_bg_inventory.delete',
                    'sotb_material_bg_inventory.validate',
                    'sotb_material_bg_inventory.reset',
                    'sotb_material_bg_inventory.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_md_inventory',
                'permissions' => [
                    // sotb_material_md_inventory Permissions
                    'sotb_material_md_inventory.view',
                    'sotb_material_md_inventory.create',
                    'sotb_material_md_inventory.edit',
                    'sotb_material_md_inventory.show',
                    'sotb_material_md_inventory.delete',
                    'sotb_material_md_inventory.validate',
                    'sotb_material_md_inventory.reset',
                    'sotb_material_md_inventory.reject',
                ]
            ],
            [
                'group_name' => 'sotb_material_sm_inventory',
                'permissions' => [
                    // sotb_material_sm_inventory Permissions
                    'sotb_material_sm_inventory.view',
                    'sotb_material_sm_inventory.create',
                    'sotb_material_sm_inventory.edit',
                    'sotb_material_sm_inventory.show',
                    'sotb_material_sm_inventory.delete',
                    'sotb_material_sm_inventory.validate',
                    'sotb_material_sm_inventory.reset',
                    'sotb_material_sm_inventory.reject',
                ]
            ],

            [
                'group_name' => 'sotb_supplier',
                'permissions' => [
                    // sotb_supplier Permissions
                    'sotb_supplier.create',
                    'sotb_supplier.view',
                    'sotb_supplier.edit',
                    'sotb_supplier.delete',
                ]
            ],

            [
                'group_name' => 'sotb_car',
                'permissions' => [
                    // sotb_car Permissions
                    'sotb_car.create',
                    'sotb_car.view',
                    'sotb_car.edit',
                    'sotb_car.delete',
                ]
            ],
            [
                'group_name' => 'sotb_driver',
                'permissions' => [
                    // sotb_driver Permissions
                    'sotb_driver.create',
                    'sotb_driver.view',
                    'sotb_driver.edit',
                    'sotb_driver.delete',
                ]
            ],
            [
                'group_name' => 'sotb_driver_car',
                'permissions' => [
                    // sotb_driver_car Permissions
                    'sotb_driver_car.create',
                    'sotb_driver_car.view',
                    'sotb_driver_car.edit',
                    'sotb_driver_car.delete',
                ]
            ],
            [
                'group_name' => 'sotb_fuel',
                'permissions' => [
                    // sotb_fuel Permissions
                    'sotb_fuel.create',
                    'sotb_fuel.view',
                    'sotb_fuel.edit',
                    'sotb_fuel.delete',
                ]
            ],
            [
                'group_name' => 'sotb_fuel_pump',
                'permissions' => [
                    // sotb_fuel_pump Permissions
                    'sotb_fuel_pump.create',
                    'sotb_fuel_pump.view',
                    'sotb_fuel_pump.edit',
                    'sotb_fuel_pump.delete',
                ]
            ],
            [
                'group_name' => 'sotb_index_pump',
                'permissions' => [
                    // sotb_index_pump Permissions
                    'sotb_index_pump.create',
                    'sotb_index_pump.view',
                    'sotb_index_pump.edit',
                    'sotb_index_pump.delete',
                ]
            ],
            [
                'group_name' => 'sotb_fuel_reception',
                'permissions' => [
                    // sotb_fuel_reception Permissions
                    'sotb_fuel_reception.create',
                    'sotb_fuel_reception.view',
                    'sotb_fuel_reception.edit',
                    'sotb_fuel_reception.show',
                    'sotb_fuel_reception.delete',
                    'sotb_fuel_reception.validate',
                    'sotb_fuel_reception.confirm',
                    'sotb_fuel_reception.approuve',
                    'sotb_fuel_reception.reset',
                    'sotb_fuel_reception.reject',
                ]
            ],
            [
                'group_name' => 'sotb_fuel_requisition',
                'permissions' => [
                    // sotb_fuel_requisition Permissions
                    'sotb_fuel_requisition.create',
                    'sotb_fuel_requisition.view',
                    'sotb_fuel_requisition.edit',
                    'sotb_fuel_requisition.show',
                    'sotb_fuel_requisition.delete',
                    'sotb_fuel_requisition.validate',
                    'sotb_fuel_requisition.confirm',
                    'sotb_fuel_requisition.approuve',
                    'sotb_fuel_requisition.reset',
                    'sotb_fuel_requisition.reject',
                ]
            ],
            [
                'group_name' => 'sotb_fuel_purchase',
                'permissions' => [
                    // sotb_fuel_purchase Permissions
                    'sotb_fuel_purchase.create',
                    'sotb_fuel_purchase.view',
                    'sotb_fuel_purchase.edit',
                    'sotb_fuel_purchase.show',
                    'sotb_fuel_purchase.delete',
                    'sotb_fuel_purchase.validate',
                    'sotb_fuel_purchase.confirm',
                    'sotb_fuel_purchase.approuve',
                    'sotb_fuel_purchase.reset',
                    'sotb_fuel_purchase.reject',
                ]
            ],
            [
                'group_name' => 'sotb_fuel_stockin',
                'permissions' => [
                    // sotb_fuel_stockin Permissions
                    'sotb_fuel_stockin.create',
                    'sotb_fuel_stockin.view',
                    'sotb_fuel_stockin.edit',
                    'sotb_fuel_stockin.show',
                    'sotb_fuel_stockin.delete',
                    'sotb_fuel_stockin.validate',
                    'sotb_fuel_stockin.confirm',
                    'sotb_fuel_stockin.approuve',
                    'sotb_fuel_stockin.reset',
                    'sotb_fuel_stockin.reject',
                ]
            ],
            [
                'group_name' => 'sotb_fuel_stockout',
                'permissions' => [
                    // sotb_fuel_stockout Permissions
                    'sotb_fuel_stockout.create',
                    'sotb_fuel_stockout.view',
                    'sotb_fuel_stockout.edit',
                    'sotb_fuel_stockout.show',
                    'sotb_fuel_stockout.delete',
                    'sotb_fuel_stockout.validate',
                    'sotb_fuel_stockout.confirm',
                    'sotb_fuel_stockout.approuve',
                    'sotb_fuel_stockout.reset',
                    'sotb_fuel_stockout.reject',
                ]
            ],
            [
                'group_name' => 'sotb_fuel_supplier_order',
                'permissions' => [
                    // sotb_fuel_supplier_order Permissions
                    'sotb_fuel_supplier_order.create',
                    'sotb_fuel_supplier_order.view',
                    'sotb_fuel_supplier_order.edit',
                    'sotb_fuel_supplier_order.delete',
                    'sotb_fuel_supplier_order.validate',
                    'sotb_fuel_supplier_order.confirm',
                    'sotb_fuel_supplier_order.approuve',
                    'sotb_fuel_supplier_order.reset',
                    'sotb_fuel_supplier_order.reject',
                ]
            ],
            [
                'group_name' => 'sotb_fuel_inventory',
                'permissions' => [
                    // sotb_fuel_inventory Permissions
                    'sotb_fuel_inventory.view',
                    'sotb_fuel_inventory.create',
                    'sotb_fuel_inventory.edit',
                    'sotb_fuel_inventory.show',
                    'sotb_fuel_inventory.delete',
                    'sotb_fuel_inventory.validate',
                    'sotb_fuel_inventory.reset',
                    'sotb_fuel_inventory.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_facture',
                'permissions' => [
                    // musumba_steel_facture Permissions
                    'musumba_steel_facture.view',
                    'musumba_steel_facture.create',
                    'musumba_steel_facture.edit',
                    'musumba_steel_facture.show',
                    'musumba_steel_facture.delete',
                    'musumba_steel_facture.validate',
                    'musumba_steel_facture.confirm',
                    'musumba_steel_facture.approuve',
                    'musumba_steel_facture.send',
                    'musumba_steel_facture.reset',
                    'musumba_steel_facture.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material',
                'permissions' => [
                    // musumba_steel_material Permissions
                    'musumba_steel_material.create',
                    'musumba_steel_material.view',
                    'musumba_steel_material.edit',
                    'musumba_steel_material.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_category',
                'permissions' => [
                    // musumba_steel_material_category Permissions
                    'musumba_steel_material_category.create',
                    'musumba_steel_material_category.view',
                    'musumba_steel_material_category.edit',
                    'musumba_steel_material_category.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_store',
                'permissions' => [
                    // musumba_steel_material_store Permissions
                    'musumba_steel_material_store.create',
                    'musumba_steel_material_store.view',
                    'musumba_steel_material_store.edit',
                    'musumba_steel_material_store.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_reception',
                'permissions' => [
                    // musumba_steel_material_reception Permissions
                    'musumba_steel_material_reception.create',
                    'musumba_steel_material_reception.view',
                    'musumba_steel_material_reception.edit',
                    'musumba_steel_material_reception.show',
                    'musumba_steel_material_reception.delete',
                    'musumba_steel_material_reception.validate',
                    'musumba_steel_material_reception.confirm',
                    'musumba_steel_material_reception.approuve',
                    'musumba_steel_material_reception.reset',
                    'musumba_steel_material_reception.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_requisition',
                'permissions' => [
                    // musumba_steel_material_requisition Permissions
                    'musumba_steel_material_requisition.create',
                    'musumba_steel_material_requisition.view',
                    'musumba_steel_material_requisition.edit',
                    'musumba_steel_material_requisition.show',
                    'musumba_steel_material_requisition.delete',
                    'musumba_steel_material_requisition.validate',
                    'musumba_steel_material_requisition.confirm',
                    'musumba_steel_material_requisition.approuve',
                    'musumba_steel_material_requisition.reset',
                    'musumba_steel_material_requisition.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_purchase',
                'permissions' => [
                    // musumba_steel_material_purchase Permissions
                    'musumba_steel_material_purchase.create',
                    'musumba_steel_material_purchase.view',
                    'musumba_steel_material_purchase.edit',
                    'musumba_steel_material_purchase.show',
                    'musumba_steel_material_purchase.delete',
                    'musumba_steel_material_purchase.validate',
                    'musumba_steel_material_purchase.confirm',
                    'musumba_steel_material_purchase.approuve',
                    'musumba_steel_material_purchase.reset',
                    'musumba_steel_material_purchase.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_stockin',
                'permissions' => [
                    // musumba_steel_material_stockin Permissions
                    'musumba_steel_material_stockin.create',
                    'musumba_steel_material_stockin.view',
                    'musumba_steel_material_stockin.edit',
                    'musumba_steel_material_stockin.show',
                    'musumba_steel_material_stockin.delete',
                    'musumba_steel_material_stockin.validate',
                    'musumba_steel_material_stockin.confirm',
                    'musumba_steel_material_stockin.approuve',
                    'musumba_steel_material_stockin.reset',
                    'musumba_steel_material_stockin.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_stockout',
                'permissions' => [
                    // musumba_steel_material_stockout Permissions
                    'musumba_steel_material_stockout.create',
                    'musumba_steel_material_stockout.view',
                    'musumba_steel_material_stockout.edit',
                    'musumba_steel_material_stockout.show',
                    'musumba_steel_material_stockout.delete',
                    'musumba_steel_material_stockout.validate',
                    'musumba_steel_material_stockout.confirm',
                    'musumba_steel_material_stockout.approuve',
                    'musumba_steel_material_stockout.reset',
                    'musumba_steel_material_stockout.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_supplier_order',
                'permissions' => [
                    // musumba_steel_material_supplier_order Permissions
                    'musumba_steel_material_supplier_order.create',
                    'musumba_steel_material_supplier_order.view',
                    'musumba_steel_material_supplier_order.edit',
                    'musumba_steel_material_supplier_order.delete',
                    'musumba_steel_material_supplier_order.validate',
                    'musumba_steel_material_supplier_order.confirm',
                    'musumba_steel_material_supplier_order.approuve',
                    'musumba_steel_material_supplier_order.reset',
                    'musumba_steel_material_supplier_order.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_inventory',
                'permissions' => [
                    // musumba_steel_material_inventory Permissions
                    'musumba_steel_material_inventory.view',
                    'musumba_steel_material_inventory.create',
                    'musumba_steel_material_inventory.edit',
                    'musumba_steel_material_inventory.show',
                    'musumba_steel_material_inventory.delete',
                    'musumba_steel_material_inventory.validate',
                    'musumba_steel_material_inventory.reset',
                    'musumba_steel_material_inventory.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_supplier',
                'permissions' => [
                    // musumba_steel_material_supplier Permissions
                    'musumba_steel_material_supplier.create',
                    'musumba_steel_material_supplier.view',
                    'musumba_steel_material_supplier.edit',
                    'musumba_steel_material_supplier.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_supplier',
                'permissions' => [
                    // musumba_steel_fuel_supplier Permissions
                    'musumba_steel_fuel_supplier.create',
                    'musumba_steel_fuel_supplier.view',
                    'musumba_steel_fuel_supplier.edit',
                    'musumba_steel_fuel_supplier.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_car',
                'permissions' => [
                    // musumba_steel_car Permissions
                    'musumba_steel_car.create',
                    'musumba_steel_car.view',
                    'musumba_steel_car.edit',
                    'musumba_steel_car.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_driver',
                'permissions' => [
                    // musumba_steel_driver Permissions
                    'musumba_steel_driver.create',
                    'musumba_steel_driver.view',
                    'musumba_steel_driver.edit',
                    'musumba_steel_driver.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_driver_car',
                'permissions' => [
                    // musumba_steel_driver_car Permissions
                    'musumba_steel_driver_car.create',
                    'musumba_steel_driver_car.view',
                    'musumba_steel_driver_car.edit',
                    'musumba_steel_driver_car.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel',
                'permissions' => [
                    // musumba_steel_fuel Permissions
                    'musumba_steel_fuel.create',
                    'musumba_steel_fuel.view',
                    'musumba_steel_fuel.edit',
                    'musumba_steel_fuel.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_pump',
                'permissions' => [
                    // musumba_steel_fuel_pump Permissions
                    'musumba_steel_fuel_pump.create',
                    'musumba_steel_fuel_pump.view',
                    'musumba_steel_fuel_pump.edit',
                    'musumba_steel_fuel_pump.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_index_pump',
                'permissions' => [
                    // musumba_steel_index_pump Permissions
                    'musumba_steel_index_pump.create',
                    'musumba_steel_index_pump.view',
                    'musumba_steel_index_pump.edit',
                    'musumba_steel_index_pump.delete',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_reception',
                'permissions' => [
                    // musumba_steel_fuel_reception Permissions
                    'musumba_steel_fuel_reception.create',
                    'musumba_steel_fuel_reception.view',
                    'musumba_steel_fuel_reception.edit',
                    'musumba_steel_fuel_reception.show',
                    'musumba_steel_fuel_reception.delete',
                    'musumba_steel_fuel_reception.validate',
                    'musumba_steel_fuel_reception.confirm',
                    'musumba_steel_fuel_reception.approuve',
                    'musumba_steel_fuel_reception.reset',
                    'musumba_steel_fuel_reception.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_requisition',
                'permissions' => [
                    // musumba_steel_fuel_requisition Permissions
                    'musumba_steel_fuel_requisition.create',
                    'musumba_steel_fuel_requisition.view',
                    'musumba_steel_fuel_requisition.edit',
                    'musumba_steel_fuel_requisition.show',
                    'musumba_steel_fuel_requisition.delete',
                    'musumba_steel_fuel_requisition.validate',
                    'musumba_steel_fuel_requisition.confirm',
                    'musumba_steel_fuel_requisition.approuve',
                    'musumba_steel_fuel_requisition.reset',
                    'musumba_steel_fuel_requisition.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_purchase',
                'permissions' => [
                    // musumba_steel_fuel_purchase Permissions
                    'musumba_steel_fuel_purchase.create',
                    'musumba_steel_fuel_purchase.view',
                    'musumba_steel_fuel_purchase.edit',
                    'musumba_steel_fuel_purchase.show',
                    'musumba_steel_fuel_purchase.delete',
                    'musumba_steel_fuel_purchase.validate',
                    'musumba_steel_fuel_purchase.confirm',
                    'musumba_steel_fuel_purchase.approuve',
                    'musumba_steel_fuel_purchase.reset',
                    'musumba_steel_fuel_purchase.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_stockin',
                'permissions' => [
                    // musumba_steel_fuel_stockin Permissions
                    'musumba_steel_fuel_stockin.create',
                    'musumba_steel_fuel_stockin.view',
                    'musumba_steel_fuel_stockin.edit',
                    'musumba_steel_fuel_stockin.show',
                    'musumba_steel_fuel_stockin.delete',
                    'musumba_steel_fuel_stockin.validate',
                    'musumba_steel_fuel_stockin.confirm',
                    'musumba_steel_fuel_stockin.approuve',
                    'musumba_steel_fuel_stockin.reset',
                    'musumba_steel_fuel_stockin.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_stockout',
                'permissions' => [
                    // musumba_steel_fuel_stockout Permissions
                    'musumba_steel_fuel_stockout.create',
                    'musumba_steel_fuel_stockout.view',
                    'musumba_steel_fuel_stockout.edit',
                    'musumba_steel_fuel_stockout.show',
                    'musumba_steel_fuel_stockout.delete',
                    'musumba_steel_fuel_stockout.validate',
                    'musumba_steel_fuel_stockout.confirm',
                    'musumba_steel_fuel_stockout.approuve',
                    'musumba_steel_fuel_stockout.reset',
                    'musumba_steel_fuel_stockout.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_supplier_order',
                'permissions' => [
                    // musumba_steel_fuel_supplier_order Permissions
                    'musumba_steel_fuel_supplier_order.create',
                    'musumba_steel_fuel_supplier_order.view',
                    'musumba_steel_fuel_supplier_order.edit',
                    'musumba_steel_fuel_supplier_order.delete',
                    'musumba_steel_fuel_supplier_order.validate',
                    'musumba_steel_fuel_supplier_order.confirm',
                    'musumba_steel_fuel_supplier_order.approuve',
                    'musumba_steel_fuel_supplier_order.reset',
                    'musumba_steel_fuel_supplier_order.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_inventory',
                'permissions' => [
                    // musumba_steel_fuel_inventory Permissions
                    'musumba_steel_fuel_inventory.view',
                    'musumba_steel_fuel_inventory.create',
                    'musumba_steel_fuel_inventory.edit',
                    'musumba_steel_fuel_inventory.show',
                    'musumba_steel_fuel_inventory.delete',
                    'musumba_steel_fuel_inventory.validate',
                    'musumba_steel_fuel_inventory.reset',
                    'musumba_steel_fuel_inventory.reject',
                ]
            ],
            [
                'group_name' => 'musumba_steel_fuel_report',
                'permissions' => [
                    'musumba_steel_fuel_report.view',
                ]
            ],
            [
                'group_name' => 'musumba_steel_material_report',
                'permissions' => [
                    'musumba_steel_material_report.view',
                ]
            ],
            [
                'group_name' => 'private_store_item',
                'permissions' => [
                    // private_store_item Permissions
                    'private_store_item.create',
                    'private_store_item.view',
                    'private_store_item.edit',
                    'private_store_item.delete',
                ]
            ],
            [
                'group_name' => 'private_drink_stockin',
                'permissions' => [
                    // private_drink_stockin Permissions
                    'private_drink_stockin.create',
                    'private_drink_stockin.view',
                    'private_drink_stockin.edit',
                    'private_drink_stockin.show',
                    'private_drink_stockin.delete',
                    'private_drink_stockin.validate',
                    'private_drink_stockin.confirm',
                    'private_drink_stockin.approuve',
                    'private_drink_stockin.reset',
                    'private_drink_stockin.reject',
                ]
            ],
            [
                'group_name' => 'private_drink_stockout',
                'permissions' => [
                    // private_drink_stockout Permissions
                    'private_drink_stockout.create',
                    'private_drink_stockout.view',
                    'private_drink_stockout.edit',
                    'private_drink_stockout.show',
                    'private_drink_stockout.delete',
                    'private_drink_stockout.validate',
                    'private_drink_stockout.confirm',
                    'private_drink_stockout.approuve',
                    'private_drink_stockout.reset',
                    'private_drink_stockout.reject',
                ]
            ],
            [
                'group_name' => 'private_drink_inventory',
                'permissions' => [
                    // private_drink_inventory Permissions
                    'private_drink_inventory.view',
                    'private_drink_inventory.create',
                    'private_drink_inventory.edit',
                    'private_drink_inventory.show',
                    'private_drink_inventory.delete',
                    'private_drink_inventory.validate',
                    'private_drink_inventory.reset',
                    'private_drink_inventory.reject',
                ]
            ],
            [
                'group_name' => 'private_store_report',
                'permissions' => [
                    // private_store_report Permissions
                    'private_store_report.view',
                    'private_store_report.edit',
                    'private_store_report.delete',
                ]
            ],
        ];


        // Do same for the admin guard for tutorial purposes
        $roleSuperAdmin = Role::create(['name' => 'superadmin', 'guard_name' => 'admin']);

        // Create and Assign Permissions
        for ($i = 0; $i < count($permissions); $i++) {
            $permissionGroup = $permissions[$i]['group_name'];
            for ($j = 0; $j < count($permissions[$i]['permissions']); $j++) {
                // Create Permission
                $permission = Permission::create(['name' => $permissions[$i]['permissions'][$j], 'group_name' => $permissionGroup, 'guard_name' => 'admin']);
                $roleSuperAdmin->givePermissionTo($permission);
                $permission->assignRole($roleSuperAdmin);
            }
        }

        // Assign super admin role permission to superadmin user
        $admin = Admin::where('username', 'superadmin')->first();
        if ($admin) {
            $admin->assignRole($roleSuperAdmin);
        }
    }
}
