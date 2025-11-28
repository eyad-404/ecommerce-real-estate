<?php
// Define the current page filename (e.g., 'properties.php')
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties - Element Real Estate</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fs-4 fw-bold" href="properties.php">Element</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    
                    <?php 
                    $is_properties = ($current_page == 'properties.php');
                    $properties_class = $is_properties ? 'active text-white' : 'text-secondary';
                    $properties_link = $is_properties ? '#' : 'properties.php'; // Remove href if active
                    $aria_current = $is_properties ? 'aria-current="page"' : '';
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $properties_class; ?>" <?php echo $aria_current; ?> href="<?php echo $properties_link; ?>">Properties</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="#">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="#">Developers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="{{ route('home') }}">Home</a>
                    </li>
                </ul>
                
                <div class="d-flex ms-auto">
                    <a class="btn btn-outline-primary" href="#">Log in</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<main class="container mt-4 mb-5">