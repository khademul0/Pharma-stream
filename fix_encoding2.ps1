$dirs = @("resources")
foreach ($dir in $dirs) {
    $files = Get-ChildItem $dir -Recurse -Filter "*.php" -ErrorAction SilentlyContinue
    foreach ($f in $files) {
        $rawBytes = [System.IO.File]::ReadAllBytes($f.FullName)
        $hasNulls = $rawBytes -contains 0
        if ($hasNulls) {
            $text = [System.Text.Encoding]::Unicode.GetString($rawBytes)
            $text = $text.TrimStart([char]0xFEFF)
            [System.IO.File]::WriteAllText($f.FullName, $text, [System.Text.UTF8Encoding]::new($false))
            Write-Host "Fixed: $($f.Name)"
        }
    }
}
Write-Host "Done."
