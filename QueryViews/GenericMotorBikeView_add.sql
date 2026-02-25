-- GENERIC LIST MOTORBIKES --

USE nteerehxgk;

-- SELECT
--     MB.id AS MOTORBIKE_ID, 
--     MB.make AS MAKE, 
--     MB.model AS MODEL,
--     MB.color AS COLOR, 
--     MR.registration_number AS REG_NO,
--     CONCAT(MC.mot_status, ' ', MC.mot_due_date) AS MOT_STATUS,
--     CONCAT(MC.road_tax_status, ' ', MC.tax_due_date) AS ROAD_TAX_STATUS,
--     MC.insurance_status AS INSURANCE_STATUS 
-- FROM 
--     motorbikes MB
--     INNER JOIN motorbike_registrations MR ON MB.id = MR.motorbike_id
--     INNER JOIN motorbike_annual_compliance MC ON MC.motorbike_id = MB.id;
--     
    
    SELECT
<<<<<<< HEAD
    MB.id AS MOTORBIKE_ID, 
=======
    MB.id AS ID, 
>>>>>>> e5e95f5ed4c72e9a134e2089e7f1c284bf715b46
    MB.make AS MAKE, 
    MB.model AS MODEL,motorbikes
    MB.engine AS 'ENGINE',
    MB.color AS COLOR, 
    MR.registration_number AS REG_NO,
    CONCAT(MC.mot_status, IFNULL(CONCAT(' ',MC.mot_due_date), '')) AS MOT_STATUS,
    CONCAT(MC.road_tax_status, IFNULL(CONCAT(' ',MC.tax_due_date), '')) AS ROAD_TAX_STATUS,
    MC.road_tax_status,
    MC.insurance_status AS INSURANCE_STATUS 
FROM 
    motorbikes MB
    INNER JOIN motorbike_registrations MR ON MB.id = MR.motorbike_id
    INNER JOIN motorbike_annual_compliance MC ON MC.motorbike_id = MB.id;
