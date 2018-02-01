<?php include 'includes/header.php' ?>

<?php include 'includes/navbar.php' ?>

<?php $currentCategory = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : null; ?>
<?php $search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : null ?>

    <main id=main class="container main" style="min-height: 70vh;">
        <section id="view-page" class="view-page">
            <div class="container">
                <div class="breadcrumb-title">
                    <?php $util::get_breadcrumb(); ?>
                </div>
                <div class=aside>
                    <div class="row">
                        <h4 class="aside-title">Filter ma recherche</h4>
                    </div>

                </div>
                <div class="content">
                    <div class="row">
                        <h2 class="currentCategory">
                            <?php if ($search != null): ?>
                                Résultat de la recherche pour : <?php echo $search; ?>
                            <?php else: ?>
                                <?php echo $currentCategory != null ? $currentCategory : 'Toutes les catégories'; ?>
                            <?php endif; ?>
                        </h2>
                        <?php if ($currentCategory != null) : ?>
                            <?php
                            if ($category->isParentCat($currentCategory)) {
                                $category->displayChild($currentCategory);
                            } else {
                                $category->displayProduct($currentCategory);
                            }
                            ?>
                        <?php elseif ($search != null): ?>
                            <?php
                            $category->displaySearch($search);
                            ?>
                        <?php else: ?>
                            <?php $category->displayAll(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

    </main>

<?php include 'includes/footer.php' ?>