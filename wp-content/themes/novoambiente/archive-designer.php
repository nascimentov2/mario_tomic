<?php   $args = array(  'post_type' => 'designer', 
                        'orderby' => 'title', 
                        'order' => 'ASC' );
?>

<?php $query = new WP_Query( $args ); ?>
<?php get_header(); ?>

        <div id="main" class="home">
            <div class="designers">
           <div id="breadcrumb" class="wrap">
               <div class="inner">
                   <a href="<?=bloginfo('url')?>">Cat&aacute;logo</a> »
                   Designers
               </div>
           </div>
           <div id="main" class="collection-list">
                   <div class="taxonomy">
                       <section id="" class="product-category">
                            <h2 class="wrap"><a href="">Designers</a></h2>
                            <div id="" class="product-carousel wrap">
                                <div class="designer-list">
                                <?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
                                        
                                        $produto = $post;

                                ?>
                                                 <li>
                                                    <a class="product-item-link" href="<?php echo $produto->guid; ?>">
                                                        <?php 
                                                        
                                                        if ( has_post_thumbnail($produto->ID)):  
                                                            $imgsource = str_replace("src", "src='".get_bloginfo('template_url')."/img/spacer.gif' data-original", get_the_post_thumbnail($produto->ID, 'thumb-home'));
                                                            echo str_replace('class="', 'class="lazy ', $imgsource);
                                                        else:
                                                            echo "<img src='".get_bloginfo('template_url')."/img/spacer.gif' data-original='".get_bloginfo('template_url')."/img/no-image-thumb.jpg' />";
                                                        endif;
             
                                                         ?>
                                                    </a>
                                                    <?php   if(isset($_COOKIE["arrml"])&&!empty($_COOKIE["arrml"])):
                                                            
                                                                    $objValores = json_decode(stripslashes($_COOKIE["arrml"]));

                                                                    if(isset($objValores)&&!empty($objValores)):

                                                                        // Transformar objeto em array
                                                                        foreach ($objValores as $key => $value){
                                                                            $arrValores[$key]= $value;
                                                                        }
                                                                        
                                                                        if(isset($arrValores)&&!empty($arrValores)):
                                                                            $mylistClass = (in_array($produto->ID, $arrValores)) ? 'remove' : '' ;
                                                                        else:
                                                                            $mylistClass = '';
                                                                        endif;
                                                                    else:
                                                                        $mylistClass = '';
                                                                    endif;
                                                            else:
                                                                $mylistClass = '';
                                                            endif; ?>
                                                 <div class="produto-meta"><a href="<?php echo $produto->guid; ?>"><?=substr(strip_tags($produto->post_title),0,200)?></a></div>
       
                                                </li>
                                        
                                    <?php endwhile; endif; ?>
                                    
                            </div></div>
                            <!--<div class="clearfix"></div>
                            <a id="prev3" class="prev" href="#">&lt;</a>
                            <a id="next3" class="next" href="#">&gt;</a>-->
                    </section>
                                
             
            </div>
        </div>
        <!-- / #main -->
<?php get_footer(); ?> 