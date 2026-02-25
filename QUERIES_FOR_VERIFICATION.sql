-- ============================================
-- QUERIES FOR QUARTERLY VEHICLE VISITS REPORT
-- ============================================
-- Period: Last 4 months from current date
-- Replace :periodStart and :periodEnd with actual dates
-- Example: '2025-07-10 00:00:00' and '2025-11-10 23:59:59'
-- ============================================

-- ============================================
-- QUERY 1: MOST VISITED VEHICLE MAKES AND MODELS
-- ============================================
-- Filter: Only models with 80+ visits
-- Returns: make (with total), model, total_visits, make_only
-- ============================================

WITH member_visits AS (
    SELECT 
        cm.make,
        cm.model,
        COUNT(*) AS total_visits
    FROM club_member_purchases cmp
    INNER JOIN club_members cm ON cmp.club_member_id = cm.id
    WHERE cmp.created_at >= :periodStart
      AND cmp.created_at <= :periodEnd
      AND cm.make IS NOT NULL AND cm.model IS NOT NULL
      AND TRIM(cm.make) != '' AND TRIM(cm.model) != ''
    GROUP BY cm.make, cm.model
    HAVING COUNT(*) >= 80
),
make_totals AS (
    SELECT 
        make,
        SUM(total_visits) AS make_total
    FROM member_visits
    GROUP BY make
)
SELECT 
    CONCAT(mt.make, ' (', mt.make_total, ')') AS make,
    mv.model,
    mv.total_visits,
    mt.make AS make_only
FROM make_totals mt
JOIN member_visits mv ON mv.make = mt.make
ORDER BY mt.make_total DESC, mv.total_visits DESC;

-- ============================================
-- QUERY 2: LEAST VISITED VEHICLE MAKES AND MODELS
-- ============================================
-- Filter: Only models with 5-80 visits
-- Returns: make (with total), model, total_visits, make_only
-- ============================================

WITH member_visits AS (
    SELECT 
        cm.make,
        cm.model,
        COUNT(*) AS total_visits
    FROM club_member_purchases cmp
    INNER JOIN club_members cm ON cmp.club_member_id = cm.id
    WHERE cmp.created_at >= :periodStart
      AND cmp.created_at <= :periodEnd
      AND cm.make IS NOT NULL AND cm.model IS NOT NULL
      AND TRIM(cm.make) != '' AND TRIM(cm.model) != ''
    GROUP BY cm.make, cm.model
    HAVING COUNT(*) <= 80 AND COUNT(*) > 5
),
make_totals AS (
    SELECT 
        make,
        SUM(total_visits) AS make_total
    FROM member_visits
    GROUP BY make
)
SELECT 
    CONCAT(mt.make, ' (', mt.make_total, ')') AS make,
    mv.model,
    mv.total_visits,
    mt.make AS make_only
FROM make_totals mt
JOIN member_visits mv ON mv.make = mt.make
ORDER BY mt.make_total DESC, mv.total_visits DESC;

-- ============================================
-- QUERY 3: MOST REPEATED MODEL + YEAR COMBINATIONS
-- ============================================
-- Filter: Only combinations with 10+ repeats
-- Returns: make, model, year, repeat_count
-- Limited to: 100 results
-- ============================================

SELECT 
    cm.make,
    cm.model,
    cm.year,
    COUNT(*) AS repeat_count
FROM club_member_purchases cmp
INNER JOIN club_members cm ON cmp.club_member_id = cm.id
WHERE cmp.created_at >= :periodStart
  AND cmp.created_at <= :periodEnd
  AND cm.make IS NOT NULL 
  AND cm.model IS NOT NULL 
  AND cm.year IS NOT NULL
  AND TRIM(cm.make) != '' 
  AND TRIM(cm.model) != ''
  AND TRIM(cm.year) != ''
GROUP BY cm.make, cm.model, cm.year
HAVING COUNT(*) >= 10
ORDER BY repeat_count DESC, cm.make ASC, cm.model ASC
LIMIT 100;

-- ============================================
-- QUERY 4: LEAST REPEATED MODEL + YEAR COMBINATIONS
-- ============================================
-- Filter: Only combinations with 1-5 repeats
-- Returns: make, model, year, repeat_count
-- Limited to: 100 results
-- ============================================

SELECT 
    cm.make,
    cm.model,
    cm.year,
    COUNT(*) AS repeat_count
FROM club_member_purchases cmp
INNER JOIN club_members cm ON cmp.club_member_id = cm.id
WHERE cmp.created_at >= :periodStart
  AND cmp.created_at <= :periodEnd
  AND cm.make IS NOT NULL 
  AND cm.model IS NOT NULL 
  AND cm.year IS NOT NULL
  AND TRIM(cm.make) != '' 
  AND TRIM(cm.model) != ''
  AND TRIM(cm.year) != ''
GROUP BY cm.make, cm.model, cm.year
HAVING COUNT(*) <= 5 AND COUNT(*) > 0
ORDER BY repeat_count ASC, cm.make ASC, cm.model ASC
LIMIT 100;

-- ============================================
-- EXAMPLE USAGE WITH ACTUAL DATES
-- ============================================
-- For the period shown in your email (10 Jul 2025 to 10 Nov 2025):
-- Replace :periodStart with '2025-07-10 00:00:00'
-- Replace :periodEnd with '2025-11-10 23:59:59'
-- ============================================

