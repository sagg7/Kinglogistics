<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = 1;
        $accountant = 2;
        $seller = 3;
        $safety = 4;
        $operations = 5;
        $dispatch = 6;
        $spotter = 7;
        $hr = 8;
        $accounting_director = 9;
        $sales_director = 10;

        // Rentals permissions
        $permission = new Permission();
        $permission->slug = 'create-rental';
        $permission->name = 'Create rental';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director, $seller]);
        $permission = new Permission();
        $permission->slug = 'read-rental';
        $permission->name = 'Read rental';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $sales_director, $seller, $operations]);
        $permission = new Permission();
        $permission->slug = 'update-rental';
        $permission->name = 'Update rental';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director, $seller]);
        $permission = new Permission();
        $permission->slug = 'delete-rental';
        $permission->name = 'Delete rental';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director, $seller]);

        // Staff permissions
        $permission = new Permission();
        $permission->slug = 'create-staff';
        $permission->name = 'Create staff';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'read-staff';
        $permission->name = 'Read staff';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $operations]);
        $permission = new Permission();
        $permission->slug = 'update-staff';
        $permission->name = 'Update staff';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'delete-staff';
        $permission->name = 'Delete staff';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);

        // Dispatch Schedule permissions
        $permission = new Permission();
        $permission->slug = 'create-dispatch-schedule';
        $permission->name = 'Create Dispatch Schedule';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'read-dispatch-schedule';
        $permission->name = 'Read Dispatch Schedule';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $hr, $operations, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'update-dispatch-schedule';
        $permission->name = 'Update Dispatch Schedule';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'delete-dispatch-schedule';
        $permission->name = 'Delete Dispatch Schedule';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);

        // Customers permissions
        $permission = new Permission();
        $permission->slug = 'create-customer';
        $permission->name = 'Create Customer';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'read-customer';
        $permission->name = 'Read Customer';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $seller, $operations, $safety, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'update-customer';
        $permission->name = 'Update Customer';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'delete-customer';
        $permission->name = 'Delete Customer';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);

        // Invoices permissions
        $permission = new Permission();
        $permission->slug = 'create-invoice';
        $permission->name = 'Create Invoice';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'read-invoice';
        $permission->name = 'Read Invoice';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'update-invoice';
        $permission->name = 'Update Invoice';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'delete-invoice';
        $permission->name = 'Delete Invoice';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director]);

        // Carriers permissions
        $permission = new Permission();
        $permission->slug = 'create-carrier';
        $permission->name = 'Create Carrier';
        $permission->save();
        $permission->roles()->attach([$admin, $hr, $sales_director, $seller]);
        $permission = new Permission();
        $permission->slug = 'read-carrier';
        $permission->name = 'Read Carrier';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $seller, $operations]);
        $permission = new Permission();
        $permission->slug = 'update-carrier';
        $permission->name = 'Update Carrier';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'delete-carrier';
        $permission->name = 'Delete Carrier';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);

        // Carriers Active permissions
        $permission = new Permission();
        $permission->slug = 'create-carrier-active';
        $permission->name = 'Create Carrier Active';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'read-carrier-active';
        $permission->name = 'Read Carrier Active';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'update-carrier-active';
        $permission->name = 'Update Carrier Active';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'delete-carrier-active';
        $permission->name = 'Delete Carrier Active';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);

        // Statements permissions
        $permission = new Permission();
        $permission->slug = 'create-statement';
        $permission->name = 'Create Statement';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'read-statement';
        $permission->name = 'Read Statement';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'update-statement';
        $permission->name = 'Update Statement';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'delete-statement';
        $permission->name = 'Delete Statement';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director]);

        // Drivers permissions
        $permission = new Permission();
        $permission->slug = 'create-driver';
        $permission->name = 'Create Driver';
        $permission->save();
        $permission->roles()->attach([$admin, $hr, $sales_director, $seller]);
        $permission = new Permission();
        $permission->slug = 'read-driver';
        $permission->name = 'Read Driver';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $seller, $operations, $safety, $dispatch, $spotter]);
        $permission = new Permission();
        $permission->slug = 'update-driver';
        $permission->name = 'Update Driver';
        $permission->save();
        $permission->roles()->attach([$admin, $hr, $operations, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'delete-driver';
        $permission->name = 'Delete Driver';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);

        // Trailers permissions
        $permission = new Permission();
        $permission->slug = 'create-trailer';
        $permission->name = 'Create Trailer';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'read-trailer';
        $permission->name = 'Read Trailer';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $seller, $operations, $safety, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'update-trailer';
        $permission->name = 'Update Trailer';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director, $operations]);
        $permission = new Permission();
        $permission->slug = 'delete-trailer';
        $permission->name = 'Delete Trailer';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);

        // Truck Active permissions
        $permission = new Permission();
        $permission->slug = 'create-truck-active';
        $permission->name = 'Create Truck Active';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'read-truck-active';
        $permission->name = 'Read Truck Active';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'update-truck-active';
        $permission->name = 'Update Truck Active';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'delete-truck-active';
        $permission->name = 'Delete Truck Active';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);

        // Trucks permissions
        $permission = new Permission();
        $permission->slug = 'create-truck';
        $permission->name = 'Create Truck';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'read-truck';
        $permission->name = 'Read Truck';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $seller, $operations, $safety, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'update-truck';
        $permission->name = 'Update Truck';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'delete-truck';
        $permission->name = 'Delete Truck';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);

        // Zones permissions
        $permission = new Permission();
        $permission->slug = 'create-zone';
        $permission->name = 'Create Zone';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'read-zone';
        $permission->name = 'Read Zone';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $seller, $operations, $safety, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'update-zone';
        $permission->name = 'Update Zone';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'delete-zone';
        $permission->name = 'Delete Zone';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);

        // Paperwork permissions
        $permission = new Permission();
        $permission->slug = 'create-paperwork';
        $permission->name = 'Create Paperwork';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'read-paperwork';
        $permission->name = 'Read Paperwork';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $seller, $operations, $safety]);
        $permission = new Permission();
        $permission->slug = 'update-paperwork';
        $permission->name = 'Update Paperwork';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);
        $permission = new Permission();
        $permission->slug = 'delete-paperwork';
        $permission->name = 'Delete Paperwork';
        $permission->save();
        $permission->roles()->attach([$admin, $hr]);

        // Loads permissions
        $permission = new Permission();
        $permission->slug = 'create-load';
        $permission->name = 'Create Load';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'read-load';
        $permission->name = 'Read Load';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $sales_director, $seller, $operations]);
        $permission = new Permission();
        $permission->slug = 'update-load';
        $permission->name = 'Update Load';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'delete-load';
        $permission->name = 'Delete Load';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);

        // Loads Dispatch permissions
        $permission = new Permission();
        $permission->slug = 'create-load-dispatch';
        $permission->name = 'Create Load Dispatch';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'read-load-dispatch';
        $permission->name = 'Read Load Dispatch';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $sales_director, $operations, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'update-load-dispatch';
        $permission->name = 'Update Load Dispatch';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'delete-load-dispatch';
        $permission->name = 'Delete Load Dispatch';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);

        // Jobs permissions
        $permission = new Permission();
        $permission->slug = 'create-job';
        $permission->name = 'Create Job';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'read-job';
        $permission->name = 'Read Job';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $seller, $operations, $safety, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'update-job';
        $permission->name = 'Update Job';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'delete-job';
        $permission->name = 'Delete Job';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);

        // Rates permissions
        $permission = new Permission();
        $permission->slug = 'create-rate';
        $permission->name = 'Create Rate';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'read-rate';
        $permission->name = 'Read Rate';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'update-rate';
        $permission->name = 'Update Rate';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'delete-rate';
        $permission->name = 'Delete Rate';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);

        // Job Opportunities permissions
        $permission = new Permission();
        $permission->slug = 'create-job-opportunity';
        $permission->name = 'Create Job Opportunity';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'read-job-opportunity';
        $permission->name = 'Read Job Opportunity';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $seller, $operations, $safety, $dispatch, $spotter]);
        $permission = new Permission();
        $permission->slug = 'update-job-opportunity';
        $permission->name = 'Update Job Opportunity';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'delete-job-opportunity';
        $permission->name = 'Delete Job Opportunity';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director]);

        // Expenses permissions
        $permission = new Permission();
        $permission->slug = 'create-expense';
        $permission->name = 'Create Expense';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'read-expense';
        $permission->name = 'Read Expense';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'update-expense';
        $permission->name = 'Update Expense';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'delete-expense';
        $permission->name = 'Delete Expense';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director]);

        // Income permissions
        $permission = new Permission();
        $permission->slug = 'create-income';
        $permission->name = 'Create Income';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'read-income';
        $permission->name = 'Read Income';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $sales_director]);
        $permission = new Permission();
        $permission->slug = 'update-income';
        $permission->name = 'Update Income';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant]);
        $permission = new Permission();
        $permission->slug = 'delete-income';
        $permission->name = 'Delete Income';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director]);

        // Tracking permissions
        $permission = new Permission();
        $permission->slug = 'create-tracking';
        $permission->name = 'Create Tracking';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'read-tracking';
        $permission->name = 'Read Tracking';
        $permission->save();
        $permission->roles()->attach([$admin, $sales_director, $operations, $safety, $dispatch, $spotter]);
        $permission = new Permission();
        $permission->slug = 'update-tracking';
        $permission->name = 'Update Tracking';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'delete-tracking';
        $permission->name = 'Delete Tracking';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);

        // Tracking History permissions
        $permission = new Permission();
        $permission->slug = 'create-tracking-history';
        $permission->name = 'Create Tracking History';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'read-tracking-history';
        $permission->name = 'Read Tracking History';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $safety, $dispatch, $spotter]);
        $permission = new Permission();
        $permission->slug = 'update-tracking-history';
        $permission->name = 'Update Tracking History';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);
        $permission = new Permission();
        $permission->slug = 'delete-tracking-history';
        $permission->name = 'Delete Tracking History';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);

        // Chat permissions
        $permission = new Permission();
        $permission->slug = 'create-chat';
        $permission->name = 'Create Chat';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $safety, $dispatch, $spotter]);
        $permission = new Permission();
        $permission->slug = 'read-chat';
        $permission->name = 'Read Chat';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $safety, $dispatch, $spotter]);
        $permission = new Permission();
        $permission->slug = 'update-chat';
        $permission->name = 'Update Chat';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $safety, $dispatch, $spotter]);
        $permission = new Permission();
        $permission->slug = 'delete-chat';
        $permission->name = 'Delete Chat';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);

        // Incidents permissions
        $permission = new Permission();
        $permission->slug = 'create-incident';
        $permission->name = 'Create Incidents';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $safety]);
        $permission = new Permission();
        $permission->slug = 'read-incident';
        $permission->name = 'Read Incidents';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $operations, $safety, $dispatch, $spotter]);
        $permission = new Permission();
        $permission->slug = 'update-incident';
        $permission->name = 'Update Incidents';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $safety]);
        $permission = new Permission();
        $permission->slug = 'delete-incident';
        $permission->name = 'Delete Incidents';
        $permission->save();
        $permission->roles()->attach([$admin, $operations]);

        // Safety Messages permissions
        $permission = new Permission();
        $permission->slug = 'create-safety-messages';
        $permission->name = 'Create Safety Message';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $safety]);
        $permission = new Permission();
        $permission->slug = 'read-safety-messages';
        $permission->name = 'Read Safety Message';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $operations, $safety, $dispatch, $spotter]);
        $permission = new Permission();
        $permission->slug = 'update-safety-messages';
        $permission->name = 'Update Safety Message';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $safety]);
        $permission = new Permission();
        $permission->slug = 'delete-safety-messages';
        $permission->name = 'Delete Safety Message';
        $permission->save();
        $permission->roles()->attach([$admin, $operations, $safety]);

        // Report Daily Loads permissions
        $permission = new Permission();
        $permission->slug = 'create-report-daily-loads';
        $permission->name = 'Create Report Daily Loads';
        $permission->save();
        $permission->roles()->attach([$admin, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'read-report-daily-loads';
        $permission->name = 'Read Report Daily Loads';
        $permission->save();
        $permission->roles()->attach([$admin, $accounting_director, $accountant, $hr, $sales_director, $seller, $operations, $safety, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'update-report-daily-loads';
        $permission->name = 'Update Report Daily Loads';
        $permission->save();
        $permission->roles()->attach([$admin, $dispatch]);
        $permission = new Permission();
        $permission->slug = 'delete-report-daily-loads';
        $permission->name = 'Delete Report Daily Loads';
        $permission->save();
        $permission->roles()->attach([$admin]);

        /*
        // Template permissions
        $permission = new Permission();
        $permission->slug = 'create-';
        $permission->name = 'Create';
        $permission->save();
        $permission->roles()->attach([$admin]);
        $permission = new Permission();
        $permission->slug = 'read-';
        $permission->name = 'Read';
        $permission->save();
        $permission->roles()->attach([$admin]);
        $permission = new Permission();
        $permission->slug = 'update-';
        $permission->name = 'Update';
        $permission->save();
        $permission->roles()->attach([$admin]);
        $permission = new Permission();
        $permission->slug = 'delete-';
        $permission->name = 'Delete';
        $permission->save();
        $permission->roles()->attach([$admin]);*/
    }
}
