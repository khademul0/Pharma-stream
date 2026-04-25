$dirs = @("app","config","database","routes","bootstrap")
foreach ($dir in $dirs) {
    $files = Get-ChildItem $dir -Recurse -Filter "*.php" -ErrorAction SilentlyContinue
    foreach ($f in $files) {
        $rawBytes = [System.IO.File]::ReadAllBytes($f.FullName)
        # Check for null bytes indicating UTF-16
        $hasNulls = $rawBytes -contains 0
        if ($hasNulls) {
            # Read as UTF-16 LE (most common on Windows)
            $text = [System.Text.Encoding]::Unicode.GetString($rawBytes)
            # Strip BOM if present and remove null chars
            $text = $text.TrimStart([char]0xFEFF)
            [System.IO.File]::WriteAllText($f.FullName, $text, [System.Text.UTF8Encoding]::new($false))
            Write-Host "Fixed: $($f.Name)"
        }
    }
}
Write-Host "Done."
