<?php
$pageTitle = "Section Manager"; // Change this per page
include './inc/head.php';
include './inc/navbar.php';
include './inc/header.php';

if (isset($_GET['sectionID']) && !empty($_GET['sectionID'])) {
    $sectionID = $utility->inputDecode($_GET['sectionID']);
    // Fetch section details from DB
    $sectionData = $model->getRows("sections", [
        "where" => ["section_slug" => $sectionID],
        "return_type" => "single"
    ]);
    if (!$sectionData) {
        $utility->setFlash("danger", "Section not found.");
        header("Location: section_mgr.php");
        exit;
    }
} else {
    $utility->setFlash("danger", "No Section ID provided.");
    header("Location: ./section_mgr.php");
    exit;
}
?>

<!-- CONTENT WRAPPER -->
<div class="ec-content-wrapper">
    <div class="content">
        <div class="breadcrumb-wrapper breadcrumb-wrapper-2 breadcrumb-contacts">
            <h1>Store Section</h1>
            <?php $utility->displayFlash(); ?>
            <p class="breadcrumbs"><span><a href="dashboard.php">Home</a></span>
                <span><i class="mdi mdi-chevron-right"></i></span>Edit Store Section
            </p>

        </div>
        <div class="row">
            <div class="col-xl-4 offset-xl-4 col-lg-12">
                <div class="ec-cat-list card card-default mb-24px">
                    <div class="card-body">
                        <div class="ec-cat-form">
                            <h4>Edit Store Section</h4>
                            <form method="POST" action="../app/admin/sectionhandler.php"
                                autocapitalize="true" autocomplete="off" id="section_edit_form">

                                <div class="form-group row">
                                    <label for="section_name" class="col-12 col-form-label">Section Name</label>
                                    <div class="col-12">
                                        <input id="section_name" name="section_name"
                                            class="form-control here slug-title" type="text"
                                            value="<?= htmlspecialchars($sectionData['section_name']) ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="slug" class="col-12 col-form-label">Section Slug</label>
                                    <div class="col-12">
                                        <input id="slug" name="slug" class="form-control here set-slug"
                                            type="text" value="<?= htmlspecialchars($sectionData['section_slug']) ?>">
                                        <small>The “slug” is the URL-friendly version of the name.
                                            It is usually all lowercase and contains only letters,
                                            numbers, and hyphens.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Section Description</label>
                                    <div class="col-12">
                                        <textarea id="fulldescription" name="fulldescription"
                                            cols="40" rows="4" class="form-control"><?= htmlspecialchars($sectionData['description']) ?></textarea>
                                    </div>
                                </div>

                                <!-- hidden inputs for action and section id -->
                                <input type="hidden" name="action" value="<?= $utility->inputEncode('this_form_edits_a_section') ?>">
                                <input type="hidden" name="section_id" value="<?= (int)$sectionData['id'] ?>">

                                <div class="row">
                                    <div class="col-12">
                                        <button name="submit" type="submit" class="btn btn-primary">Update Section</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End Content -->
</div> <!-- End Content Wrapper -->
<?php
include './inc/footer.php';
?>