(function () {
  // Polyfill for NodeList.prototype.forEach() in IE
  if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = function (callback, thisArg) {
      thisArg = thisArg || window;
      for (var i = 0; i < this.length; i++) {
        callback.call(thisArg, this[i], i, this);
      }
    };
  }

  // Variables

  var play_mobile_nav_container = document.querySelector(".play_mobile_nav_container");
  var navToggle = document.querySelector(".play_mob_nav_check");

  var play_window = document.querySelector(".play_window");

  var lang = document.querySelector(".m_w_languages");
  var langToggle = document.querySelectorAll(".mwh_lang_toggle");

  var search = document.querySelector(".m_w_search");
  var searchToggle = document.querySelectorAll(".mwh_search_toggle");

  var closeToggle = document.querySelectorAll(".header__close--toggle");

  var allElementsToClose = document.querySelectorAll(".play_window, .m_w_languages, .m_w_search");

  var emailGlobalUnsub = document.querySelector('input[name="globalunsub"]');

  // Functions

  // Function for executing code on document ready
  function domReady(callback) {
    if (["interactive", "complete"].indexOf(document.readyState) >= 0) {
      callback();
    } else {
      document.addEventListener("DOMContentLoaded", callback);
    }
  }

  // Function for toggling mobile navigation
  function toggleNav() {
    play_mobile_nav_container.classList.toggle("open");
  }

  // Function for toggling mobile language selector
  function toggleLang() {
    play_window.classList.toggle("open");
    lang.classList.toggle("open");
  }

  // Function for toggling mobile search field
  function toggleSearch() {
    play_window.classList.toggle("open");
    search.classList.toggle("open");
  }

  // Function for the header close option on mobile
  function closeAll() {
    allElementsToClose.forEach(function (element) {
      element.classList.remove("open");
    });
    document.getElementById("goTop").classList.remove("z0");
    document.querySelector(".header").classList.remove("z0");
  }

  // Function to disable the other checkbox inputs on the email subscription system page template
  function toggleDisabled() {
    var emailSubItem = document.querySelectorAll("#email-prefs-form .item");

    emailSubItem.forEach(function (item) {
      var emailSubItemInput = item.querySelector("input");

      if (emailGlobalUnsub.checked) {
        item.classList.add("disabled");
        emailSubItemInput.setAttribute("disabled", "disabled");
        emailSubItemInput.checked = false;
      } else {
        item.classList.remove("disabled");
        emailSubItemInput.removeAttribute("disabled");
      }
    });
  }

  // Execute JavaScript on document ready
  domReady(function () {
    if (!document.body) {
      return;
    } else {
      // Function dependent on language switcher
      if (langToggle) {
        Array.prototype.forEach.call(langToggle, function (el) {
          el.addEventListener("click", toggleLang);
        });
      }

      // Function dependent on navigation
      if (navToggle) {
        navToggle.addEventListener("change", toggleNav);
      }

      // Function dependent on search field
      if (searchToggle) {
        Array.prototype.forEach.call(searchToggle, function (el) {
          el.addEventListener("click", toggleSearch);
        });
      }

      // Function dependent on close toggle
      if (closeToggle) {
        Array.prototype.forEach.call(closeToggle, function (el) {
          el.addEventListener("click", closeAll);
        });
      }

      // Function dependent on email unsubscribe from all input
      if (emailGlobalUnsub) {
        emailGlobalUnsub.addEventListener("change", toggleDisabled);
      }
    }
  });
})();

/* Go to top button & Sticky bar */

var y_scroll = 600;
var end_scroll = 1200;
var mybutton = document.getElementById("goTop");
var stickyBar = document.getElementById("play_sticky_bar");

window.onscroll = function () {
  scrollFunction();
};

function scrollFunction() {
  var top = this.scrollY;
  if (top > y_scroll && top < document.body.scrollHeight - end_scroll) {
    mybutton.classList.add("open");
    if (stickyBar) {
      stickyBar.classList.add("open");
    }
  } else {
    mybutton.classList.remove("open");
    if (stickyBar) {
      stickyBar.classList.remove("open");
    }
  }
}

function goToTop() {
  document.documentElement.scrollTop = 0;
}
