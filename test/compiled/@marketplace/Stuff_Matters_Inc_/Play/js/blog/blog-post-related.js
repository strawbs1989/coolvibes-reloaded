<html>
 <head></head>
 <body>
  var inc=0,states=["mgi-col1","mgi-col2"],cols=document.querySelector(".rp_cols").getAttribute("data-cols");"3col"==cols&amp;&amp;states.push("mgi-col3");const date_options={year:"numeric",month:"short",day:"numeric"};var blog_post_formatter=function(blogposts){for(var formatted='
  <div class="play_grid-container">
   <section class="play_grid-inner">
    ',i=0;i
    <blogposts.length;i++){var blogpost="blogposts[i];formatted+='<article" class="play_grid-col play_grid--'+cols+" "+states[inc]+'">
     ',formatted+='
     <div class="play_widget-container play_widget-single_post">
      ',formatted+=`
      <a class="play_widget_post_item-a mw_single_post" href="${blogpost.url}">`,blogpost.featuredImage&amp;&amp;(formatted+=`<img src="${blogpost.featuredImage}" alt="${blogpost.featuredImageAltText}" width="100%" loading="lazy">`),formatted+='
       <div class="play_widget_post_item-text">
        ',formatted+=`
        <h4 class="play_widget_post_item-title">${blogpost.name}</h4>`,formatted+=`
        <time class="play_widget_post_item-date">${new Date(blogpost.publishDate).toLocaleDateString("en-EN",date_options)}</time>`,formatted+="
       </div></a>
     </div>",formatted+="",inc=++inc%states.length}return formatted+="
    </blogposts.length;i++){var>
   </section>
  </div>"}; //# sourceURL=https://cdn1.hubspotusercontent-eu1.net/hub/26937672/hub_generated/template_assets/61692183278/1676287495979/marketplace/Stuff_Matters_Inc_/Play/js/blog/blog-post-related.js
 </body>
</html>