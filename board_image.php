<?php
// board_image.php - a banner displaying script
// ---------------
//
// I would name it "banner.php", but most adblocks have that blocked,
// degrading the site quality for certain users.

/* currently overloads and crashes php? disabling...?
$board = isset($_GET['b']) ? $_GET['b'] : '';
$dir = "static/banners/";
$domain = "/";

// Let's sanitize it for POSIX machines:
$board = str_replace("\0", '', $board); // \0 can be used to "cut the end" of the path
$board = str_replace("/", '', $board); // / can be used to traverse subdirectories
if ($board[0] == '.') { // If it starts with zero, it's either a hidden file, or ./..
  $board = "Z".$board;  // (we ignore the first case and second case is dangerous)
}
if (!$board) {
  $board = "?"; // Invalid boardname
}

$banners = glob($dir.$board."/*");
while (!$banners) { // If the previous call failed or no banners
  $boards = glob($dir."/*"); // We get all the boards
  $board = basename($boards[array_rand($boards)]); // we pick the random
  $banners = glob($dir.$board."/*"); // we select banners of this board nao
}
$banner = $banners[array_rand($banners)]; // we pick a random banner

header("Location: ".$domain.$banner);
*/
