<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE VIEW motorbike_records_view AS

            -- RENTING APPLICATION --
            select
                CONCAT(m.id,'_',rbi.booking_id) id,m.id MID,m.reg_no VRM,'RENTING (ACTIVE)' as 'DATABASE',rbi.booking_id as 'DOC_ID',
                (select CONCAT(first_name,' ',last_name) from customers where id = (select customer_id from renting_bookings where id = rbi.booking_id ) ) as 'PERSON',
                rbi.start_date as START_DATE,
                rbi.end_date END_DATE
            from renting_booking_items rbi
            inner join motorbikes m on m.id = rbi.motorbike_id
            where rbi.end_date is null

            UNION

            -- RENTED APPLICATION --
            select
                CONCAT(m.id,'_',rbi.booking_id) id,m.id MID,m.reg_no VRM,'RENTED' as 'DATABASE',rbi.booking_id as 'DOC_ID',
                (select CONCAT(first_name,' ',last_name) from customers where id = (select customer_id from renting_bookings where id = rbi.booking_id ) ) as 'PERSON',
                rbi.start_date as START_DATE,
                rbi.end_date END_DATE
            from renting_booking_items rbi
            inner join motorbikes m on m.id = rbi.motorbike_id
            where rbi.end_date is not null

            union

            -- FINANCE LOGBOOK TRANSFER --
            SELECT CONCAT(m.id,'_',fa.id) id,m.id MID,m.reg_no VRM,'FINANCE LOGBOOK TRANSFERRED' as 'DATABASE', fa.id as 'DOC_ID',
                (select CONCAT(first_name,' ',last_name) from customers where id = fa.customer_id ) as 'PERSON',
                fa.contract_date START_DATE,fa.logbook_transfer_date END_DATE
            FROM finance_applications fa
            inner join application_items ai on ai.application_id = fa.id
            inner join motorbikes m on m.id  = ai.motorbike_id
            where fa.deposit != extra+motorbike_price  and extra+motorbike_price > 100 AND fa.log_book_sent = 1

            union

            -- FINANCE --
            SELECT CONCAT(m.id,'_',fa.id) id,m.id MID,m.reg_no VRM,'FINANCE (ACTIVE)' as 'DATABASE', fa.id as 'DOC_ID',
                (select CONCAT(first_name,' ',last_name) from customers where id = fa.customer_id ) as 'PERSON',
                fa.contract_date START_DATE,fa.logbook_transfer_date END_DATE
            FROM finance_applications fa
            inner join application_items ai on ai.application_id = fa.id
            inner join motorbikes m on m.id  = ai.motorbike_id
            where fa.deposit != extra+motorbike_price  and extra+motorbike_price AND fa.log_book_sent = 0 and fa.is_cancelled = 0

            union

            -- CASH SOLD NEW --
            SELECT CONCAT(m.id,'_',fa.id) id,m.id MID,m.reg_no VRM,'NEW SOLD' as 'DATABASE', fa.id as 'DOC_ID',
                (select CONCAT(first_name,' ',last_name) from customers where id = fa.customer_id ) as 'PERSON',
                fa.contract_date START_DATE,fa.logbook_transfer_date END_DATE
            FROM finance_applications fa
            inner join application_items ai on ai.application_id = fa.id
            inner join motorbikes m on m.id  = ai.motorbike_id
            where fa.deposit = extra+motorbike_price and extra+motorbike_price > 100 and fa.log_book_sent = 1

            union

            -- CASH SOLD NEW NO-LOGBOOK--
            SELECT CONCAT(m.id,'_',fa.id) id,m.id MID,m.reg_no VRM,'NEW SOLD (NO LOGBOOK TRANSFERRED)' as 'DATABASE', fa.id as 'DOC_ID',
                (select CONCAT(first_name,' ',last_name) from customers where id = fa.customer_id ) as 'PERSON',
                fa.contract_date START_DATE,fa.logbook_transfer_date END_DATE
            FROM finance_applications fa
            inner join application_items ai on ai.application_id = fa.id
            inner join motorbikes m on m.id  = ai.motorbike_id
            where fa.deposit = extra+motorbike_price and extra+motorbike_price > 100 and fa.log_book_sent = 0

            union

            -- CASH SOLD NEW --
            SELECT CONCAT(m.id,'_',fa.id) id,m.id MID,m.reg_no VRM,'FINANCE (TERMINATE)' as 'DATABASE', fa.id as 'DOC_ID',
                (select CONCAT(first_name,' ',last_name) from customers where id = fa.customer_id ) as 'PERSON',
                fa.contract_date START_DATE,fa.cancelled_at END_DATE
            FROM finance_applications fa
            inner join application_items ai on ai.application_id = fa.id
            inner join motorbikes m on m.id  = ai.motorbike_id
            where fa.deposit != extra+motorbike_price  and extra+motorbike_price AND fa.log_book_sent = 0 AND fa.is_cancelled = 1

            union

            -- CLAIM --
            SELECT
                CONCAT(m.id,'_',cm.id) id,m.id MID, m.reg_no VRM, 'CLAIM (ACTIVE)' as 'DATABASE', cm.id as 'DOC_ID',
                cm.fullname 'PERSON', cm.received_date START_DATE,
                cm.returned_date as END_DATE FROM claim_motorbikes cm
            inner join motorbikes m on m.id = cm.motorbike_id

            union

            -- MOT --
            select CONCAT(m.id,'_',mb.id) id,m.id MID, m.reg_no VRM, 'MOT' as 'DATABASE', mb.id as 'DOC_ID',
                mb.customer_name 'PERSON', mb.start as START_DATE,
                mb.updated_at as END_DATE from mot_bookings mb
            inner join motorbikes m on m.reg_no = REPLACE(mb.vehicle_registration,' ','')

            union

            -- USED --
            SELECT CONCAT(m.id,'_',ms.id) id,m.id MID, m.reg_no VRM, 'USED' as 'DATABASE', ms.id as 'DOC_ID',
                '' as 'PERSON', ms.created_at START_DATE,
                null as END_DATE
            from motorbikes_sale ms
            inner join motorbikes m on m.id = ms.motorbike_id
            where ms.is_sold = 0

            union

            -- USED SOLD --
            SELECT CONCAT(m.id,'_',ms.id) id,m.id MID, m.reg_no VRM, 'USED SOLD' as 'DATABASE', ms.id as 'DOC_ID',
                '-' as 'PERSON', ms.created_at START_DATE,
                ms.updated_at as END_DATE
            from motorbikes_sale ms
            inner join motorbikes m on m.id = ms.motorbike_id
            where ms.is_sold = 1

            union

            -- COMPANY VEH --
            select CONCAT(m.id,'_',cv.id) id,m.id MID, m.reg_no VRM, 'COMPANY VEHICLE' as 'DATABASE', cv.id as 'DOC_ID',
                cv.custodian as 'PERSON', cv.created_at as START_DATE,
                cv.updated_at as END_DATE
            from company_vehicles cv
            inner join motorbikes m on m.id = cv.motorbike_id

            union

            -- PURCHASED VEHICLE --
            SELECT CONCAT(m.id,'_',puv.id) id,m.id MID, m.reg_no VRM, 'PURCHASED VEHICLE' as 'DATABASE', puv.id as 'DOC_ID',
                puv.full_name as 'PERSON', puv.created_at  as START_DATE,
                puv.updated_at END_DATE
            from purchase_used_vehicles puv
            inner join motorbikes m  on m.reg_no =  REPLACE(puv.reg_no,' ','')

            union

            -- NEW VEHICLES NOVRM --
            SELECT CONCAT(nm.id,'_',nm.id) id,nm.VIM as MID, nm.VIM as VRM, 'NEW VEHICLES NOVRM' as 'DATABASE', nm.id as 'DOC_ID',
                'TBD' as 'PERSON', nm.purchase_date as START_DATE,
                nm.updated_at as END_DATE
            from new_motorbikes nm
            where nm.is_vrm = 0 AND nm.is_migrated = 0

            union

            -- NEW VEHICLES VRM --
            SELECT CONCAT(m.id,'_',nm.id) id,m.id MID, m.reg_no VRM, 'NEW VEHICLES' as 'DATABASE', nm.id as 'DOC_ID',
                'TBD' as 'PERSON', nm.purchase_date as START_DATE,
                nm.updated_at as END_DATE
            from new_motorbikes nm
            inner join motorbikes m on m.reg_no = REPLACE(nm.VRM,' ','')
            where nm.is_vrm = 1 AND nm.is_migrated = 0

            union

            -- REPAIR VEHICLES --
            SELECT CONCAT(m.id,'_',mr.id) id,m.id MID, m.reg_no VRM, 'REPAIR' as 'DATABASE', mr.id as 'DOC_ID',
                mr.fullname as 'PERSON', mr.arrival_date as 'START_DATE', mr.returned_date as END_DATE
            from motorbikes_repair mr
            inner join motorbikes m on m.id = mr.motorbike_id

            union

            -- RECOVERED VEHICLES --
            SELECT CONCAT(m.id,'_',rm.id) id, m.id MID, m.reg_no VRM, 'RECOVERED' as 'DATABASE', rm.id as 'DOC_ID',
                '' as 'PERSON', rm.case_date as 'START_DATE', rm.returned_date as END_DATE
            from recovered_motorbikes rm
            inner join motorbikes m on m.id = rm.motorbike_id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW motorbike_records_view');
    }
};
