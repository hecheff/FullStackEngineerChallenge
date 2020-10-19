<?php 
    include($_SERVER['DOCUMENT_ROOT'].'/php/common.php');

    

    // Check if user is logged in
    if (empty($GLOBALS['user_details'])) {
        header('location: /');
    }
    $review_id = htmlspecialchars($_GET['id']);
    // Check if review exists
    $review = GetTableEntries("reviews", $review_id);
    if (!$review) {
        header('location: /');
    }
    // If non-admin, only proceed if user is assigned to give feedback for this review
    if (!$GLOBALS['user_details']['is_admin']) {
        $review_feedback = GetReviewFeedbackListByStaffID($GLOBALS['user_details']['staff_id']);
        if (!$review_feedback) {
            header('location: /');
        }
    } 

    if (!empty($GLOBALS['user_details']) && $GLOBALS['user_details']['is_admin']) {
        $staff_list             = GetTableEntries("staff");
        $review_feedback_list   = GetReviewFeedbackEntriesByReviewID($review_id);
    }

    $review_feedback_info = GetReviewFeedbackByReviewIDAndStaffID($review['id'], $GLOBALS['user_details']['staff_id']);
?>

<html>
    <?php include($_SERVER['DOCUMENT_ROOT'].'/php/head_contents.php'); ?>

    <body>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/php/header.php'); ?>
        <div class="main_content">
            <div class="content_wrapper">
                <div class="notices">
                    <?php echo $_SESSION['message']; ?>&nbsp;
                </div>

                <div class="title_row panel_highlight_normal">Performance Review</div>
                <div class="main panel_main_normal">
                    <table class="2col">
                        <tr>
                            <th width="20%">Staff Reviewed: </th>
                            <td width="80%">
                                <?php 
                                    $staff_details = GetTableEntries("staff", $review['staff_id']);
                                    echo $staff_details['last_name'].", ".$staff_details['first_name'];
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Review Contents: </th>
                            <td>
                                <?php echo nl2br($review['review_contents']); ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php if (!empty($review_feedback_info) && !$review_feedback_info['feedback_date_completed']) : ?>
                    <div class="title_row panel_highlight_normal">Create Feedback</div>
                    <div class="main panel_main_normal">
                        <form action="/php/submit_feedback.php" method="post" onsubmit='return confirm("Are you sure you want to submit this feedback?");'>
                            <input type="hidden" id="id" name="id" value="<?php echo $review_feedback_info['id']; ?>">
                            <table class="2col">
                                <tr>
                                    <td>
                                        <textarea id="feedback_content" name="feedback_content" class="review_contents" required></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="submit" value="Submit Feedback"></form>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                <?php elseif (!empty($review_feedback_info) && $review_feedback_info['feedback_date_completed']) : ?>
                    <div class="title_row panel_highlight_normal">Submitted Feedback</div>
                    <div class="main panel_main_normal">
                        <table class="2col">
                            <tr>
                                <th width="20%">Date Submitted</th>
                                <th width="80%">Feedback</th>
                            </tr>
                            <tr>
                                <td><?php echo $review_feedback_info['feedback_date_completed']; ?></td>
                                <td><?php echo $review_feedback_info['feedback_content']; ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if (!empty($GLOBALS['user_details']) && $GLOBALS['user_details']['is_admin']) : ?>
                    <div class="title_row panel_highlight_admin">Assign Feedback Request</div>
                    <div class="main panel_main_admin">
                        <form action="/php/admin_exec.php?type=review_feedback&action=add" method="post">
                            <input type="hidden" id="review_id" name="review_id" value="<?php echo $review_id; ?>">
                            <table class="2col">
                                <tr>
                                    <th width="20%">Staff to Request</th>
                                    <th width="70%">Comment</th>
                                    <th>&nbsp;</th>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="feedback_staff_id" name="feedback_staff_id" required>
                                            <option value="">- Select a staff member -</option>
                                            <?php OutputListAsOptions($staff_list, "last_name", "first_name", ", "); ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" id="assigner_staff_comment" name="assigner_staff_comment" style="width:100%;" 
                                            value="Please provide feedback for this performance review.">
                                    </td>
                                    <td style="text-align:right;"><input type="submit" value="Assign"></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if (!empty($GLOBALS['user_details']) && $GLOBALS['user_details']['is_admin']) : ?>
                    <div class="title_row panel_highlight_admin">Review Feedback List</div>
                    <div class="main panel_main_admin">
                        <table class="review_list">
                            <tr>
                                <th>Review Date</th>
                                <th>Author</th>
                                <th width="50%">Feedback Content</th>
                                <th>Status</th>
                                <th>Assigned By</th>
                                <th>&nbsp;</th>
                            </tr>
                                <?php foreach ($review_feedback_list as $feedback_entry) { ?>
                                    <tr>
                                        <td>
                                            <?php echo !empty($feedback_entry['feedback_date_completed']) ? $feedback_entry['feedback_date_completed'] : "--"; ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $staff_details = GetTableEntries("staff", $feedback_entry['feedback_staff_id']);
                                                echo $staff_details['last_name'].", ".$staff_details['first_name'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo !empty($feedback_entry['feedback_content']) ? $feedback_entry['feedback_content'] : "--"; ?>
                                        </td>
                                        <td>
                                            <?php echo !empty($feedback_entry['feedback_date_completed']) ? "Complete" : "Pending"; ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $staff_details = GetTableEntries("staff", $feedback_entry['assigner_staff_id']);
                                                echo $staff_details['last_name'].", ".$staff_details['first_name'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php //echo $feedback_entry['']; ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if (empty($review_feedback_list)) { ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center;">No feedback requests assigned.</td>
                                    </tr>    
                                <?php } ?>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'].'/php/footer.php'); ?>
    </body>
</html>