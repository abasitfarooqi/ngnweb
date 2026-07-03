<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script('
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $size = 9;
            $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
            $y = $pdf->get_height() - 22;
            $x = $pdf->get_width() - 95;
            $pdf->text($x, $y, $pageText, $font, $size);
        ');
    }
</script>
