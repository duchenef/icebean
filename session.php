<php session_start(); ?>

<form action="process.php" method="post">
  <h1>Shirt Order Form</h1>
  <label for="color">Shirt Color</label>
  <select id="color" name="shirt_color">
    <option value="red">Red</option>
    <option value="yellow">Yellow</option>
    <option value="purple">Purple</option>
    <option value="blue">Blue</option>
    <option value="green">Green</option>
    <option value="orange">Orange</option>
  </select>
  <input type="submit" value="Submit">
</form>