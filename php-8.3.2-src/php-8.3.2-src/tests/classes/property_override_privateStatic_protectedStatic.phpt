--TEST--
Redeclare inherited private static property as protected static.
--FILE--
<?php
  class A
  {
      private static $p = "A::p (static)";
      static function showA()
      {
          echo self::$p . "\n";
      }
  }

  class B extends A
  {
      protected static $p = "B::p (static)";
      static function showB()
      {
          echo self::$p . "\n";
      }
  }


  A::showA();

  B::showA();
  B::showB();
?>
--EXPECT--
A::p (static)
A::p (static)
B::p (static)
