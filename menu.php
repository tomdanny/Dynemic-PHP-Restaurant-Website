<?php 
  define("TITLE", "Menu | Franklin's Fine Dining");

  include('includes/header.php');
?>

  <div id="menu-items">
    
    <h1>Our Delicious Menu</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi nulla accusamus vitae dolor totam quis! Optio blanditiis voluptates non suscipit maxime, hic. Id quos veritatis corrupti, animi repellat consectetur dolore.</p>
    <p><em>Click any menu item to learn more about it.</em></p>

    <hr>

    <ul>
      <?php foreach($menuItems as $dish => $item) { ?>
      <li><a href="dish.php?item=<?php echo $dish; ?>"><?php echo $item[title]; ?></a> <sup>$</sup><?php echo $item[price]; ?></li>
      <?php } ?>
    </ul>
  
  </div><!-- menu-items -->

<?php 
  include('includes/footer.php');
?>