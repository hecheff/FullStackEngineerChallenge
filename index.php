<?php 
    include($_SERVER['DOCUMENT_ROOT'].'/php/common.php');

    if (!empty($GLOBALS['user_details']) && $GLOBALS['user_details']['is_admin']) {
        $staff_list             = GetTableEntries("staff");
        $review_list            = GetTableEntries("reviews");
        $review_feedback_list   = GetTableEntries("review_feedback");
    }
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

                <?php if (!empty($GLOBALS['user_details']) && $GLOBALS['user_details']['is_admin']) : ?>
                    <div class="title_row panel_highlight_admin">Create Performance Review</div>
                    <div class="main panel_main_admin">
                        <form action="/php/admin_exec.php?type=review&action=add" method="post">
                            <table class="2col">
                                <tr>
                                    <th width="20%">Staff Reviewed: </th>
                                    <td width="80%">
                                        <select id="staff_id" name="staff_id" required>
                                            <option value="">- Select a staff member -</option>
                                            <?php OutputListAsOptions($staff_list, "last_name", "first_name", ", "); ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Review Contents: </th>
                                    <td>
                                        <textarea id="review_contents" name="review_contents" class="review_contents" 
                                            placeholder="Add performance review contents here." required></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="submit_row" colspan="2">
                                        <input type="submit" value="Create Review">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($GLOBALS['user_details'])) : ?>
                    <div class="title_row panel_highlight_normal">Performance Review Feedback Requests</div>
                    <div class="main panel_main_normal">
                        <table class="">
                            <?php $user_feedback_request_list = GetReviewFeedbackRequestsByAssignedStaffID($GLOBALS['user_details']['staff_id'], true); ?>
                            <?php if (!empty($user_feedback_request_list)): ?>
                                <tr>
                                    <th>Date of Request</th>
                                    <th>Staff To Review</th>
                                    <th>Assigned By</th>
                                    <th>Comment</th>
                                    <th>&nbsp;</th>
                                </tr>
                                <?php foreach ($user_feedback_request_list as $feedback_entry) { ?>
                                    <td><?php echo $feedback_entry['date_created']; ?></td>
                                    <td>
                                        <?php 
                                            $staff_details = GetTableEntries("staff", $feedback_entry['feedback_staff_id']);
                                            echo $staff_details['last_name'].", ".$staff_details['first_name'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $staff_details = GetTableEntries("staff", $feedback_entry['assigner_staff_id']);
                                            echo $staff_details['last_name'].", ".$staff_details['first_name'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo $feedback_entry['assigner_staff_comment']; ?>
                                    </td>
                                    <td>
                                        <button onclick="window.location.href='/review.php?id=<?php echo $feedback_entry['review_id']; ?>';">Go to Review</button>    
                                    </td>
                                <?php } ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align:center;">No pending reviews.</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if (!empty($GLOBALS['user_details']) && $GLOBALS['user_details']['is_admin']) : ?>
                    <div class="title_row panel_highlight_admin">Reviews List</div>
                    <div class="main panel_main_admin">
                        <table class="review_list">
                            <tr>
                                <th>Review Date<br>(Last Updated)</th>
                                <th>Staff Reviewed</th>
                                <th width="40%">Content (Preview)</th>
                                <th>Feedback Status</th>
                                <th>&nbsp;</th>
                            </tr>
                            <?php foreach ($review_list as $review) { ?>
                                <?php
                                    $staff_details = GetTableEntries("staff", $review['staff_id']);
                                    $staff_name = $staff_details['last_name'].", ".$staff_details['first_name'];

                                    // Get list of staff who are assigned to give feedback to this

                                ?>
                                <tr>
                                    <td><?php echo $review['date_created']; ?><br>(<?php echo $review['date_updated']; ?>)</td>
                                    <td><?php echo $staff_name; ?></td>
                                    <td><?php echo mb_substr(nl2br($review['review_contents']), 0, REVIEW_CHARACTER_PREVIEW_LIMIT).(count_chars($review['review_contents']) > REVIEW_CHARACTER_PREVIEW_LIMIT ? "..." : ""); ?></td>
                                    <td></td>
                                    <td style="text-align:right;">
                                        <button onclick="window.location.href='/review.php?id=<?php echo $review['id']; ?>';">Go to Review</button><br>
                                        <button onclick="toggle_editPanel('editReview_<?php echo $review['id']; ?>');">Edit</button>
                                    </td>
                                </tr>
                                <tr id="editReview_<?php echo $review['id']; ?>" class="edit_panel" style="display:none;">
                                    <td colspan="5">
                                        <form action="/php/admin_exec.php?type=review&action=edit" method="post">
                                            <input type="hidden" id="id" name="id" value="<?php echo $review['id']; ?>">
                                            <table class="2col">
                                                <tr><th>Review Contents:</th></tr>
                                                <tr>
                                                    <td>
                                                        <textarea id="review_contents" name="review_contents" class="review_contents" required><?php echo $review['review_contents']; ?></textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="submit" value="Update Review"></form>
                                                    <form action='./php/admin_exec.php?type=review&action=delete' method='post' onsubmit='return confirm("Delete this performance review?");'>
                                                        <input type='hidden' id='id' name='id' value='<?php echo $review['id']; ?>'>
                                                        <input type='submit' value='Delete'>
                                                    </form>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                <?php endif; ?>

                <?php if (!empty($GLOBALS['user_details']) && $GLOBALS['user_details']['is_admin']) : ?>
                <div class="title_row panel_highlight_admin">Create Staff Entry</div>
                <div class="main panel_main_admin">
                    <form action="/php/admin_exec.php?type=staff&action=add" method="post">
                        <table class="2col staff_entry">
                            <tr>
                                <th width="20%">Username: </th>
                                <td width="80%"><input type="text" id="username" name="username" class="input_username" maxlength="32" required></td>
                            </tr>
                            <tr>
                                <th>Password: </th>
                                <td><input type="password" id="password" name="password" maxlength="255" required></td>
                            </tr>
                            <tr>
                                <th>First Name: </th>
                                <td><input type="text" id="first_name" name="first_name" maxlength="30" class="input_name" required></td>
                            </tr>
                            <tr>
                                <th>Last Name: </th>
                                <td><input type="text" id="last_name" name="last_name" maxlength="30" class="input_name" required></td>
                            </tr>
                            <tr>
                                <th>Email Address: </th>
                                <td><input type="email" id="email" name="email" maxlength="50" class="input_email" required></td>
                            </tr>
                            <tr>
                                <th>Admin Access: </th>
                                <td><input type="checkbox" id="is_admin" name="is_admin" value="1"></td>
                            </tr>
                            <tr>
                                <td class="submit_row" colspan="2">
                                    <input type="submit" value="Register New Staff Member">
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <?php endif; ?>

                <?php if (!empty($GLOBALS['user_details']) && $GLOBALS['user_details']['is_admin']) : ?>
                    <div class="title_row panel_highlight_admin">Staff List</div>
                    <div class="main panel_main_admin">
                        <table class="staff_list">
                            <tr>
                                <th class='id'>Staff ID</th>
                                <th class='username'>Username</th>
                                <th class='first_name'>First Name</th>
                                <th class='last_name'>Last Name</th>
                                <th class='email'>Email Address</th>
                                <th class='access_level'>Access Level</th>
                                <th class='timestamp'>Date Created</th>
                                <th class='timestamp'>Last Updated</th>
                                <th>&nbsp;</th>
                            </tr>
                            <?php 
                                foreach ($staff_list as $staff) {
                            ?>
                                <tr>
                                    <td><?php echo $staff['id']; ?></td>
                                    <td><?php echo $staff['username']; ?></td>
                                    <td><?php echo $staff['first_name']; ?></td>
                                    <td><?php echo $staff['last_name']; ?></td>
                                    <td><?php echo $staff['email']; ?></td>
                                    <td><?php echo (($staff['is_admin'] == 1) ? "Admin" : "User"); ?></td>
                                    <td><?php echo $staff['date_created']; ?></td>
                                    <td><?php echo $staff['date_updated']; ?></td>
                                    <td><button onclick="toggle_editPanel('editStaff_<?php echo $staff['id']; ?>');">Edit</button></td>
                                </tr>

                                <tr id="editStaff_<?php echo $staff['id']; ?>" class="edit_panel" style="display:none;">
                                    <td colspan="9">
                                        <form action="/php/admin_exec.php?type=staff&action=edit" method="post">
                                            <input type="hidden" id="id" name="id" value="<?php echo $staff['id']; ?>">
                                            <table class="staff_entry">
                                                <tr>
                                                    <th>First Name: </th>
                                                    <td><input type="text" id="first_name" name="first_name" maxlength="30" class="input_name" value="<?php echo $staff['first_name']; ?>" required></td>
                                                </tr>
                                                <tr>
                                                    <th>Last Name: </th>
                                                    <td><input type="text" id="last_name" name="last_name" maxlength="30" class="input_name" value="<?php echo $staff['last_name']; ?>" required></td>
                                                </tr>
                                                <tr>
                                                    <th>Email Address: </th>
                                                    <td><input type="email" id="email" name="email" maxlength="50" class="input_email" value="<?php echo $staff['email']; ?>" required></td>
                                                </tr>
                                                <tr>
                                                    <th>Admin Access: </th>
                                                    <td><input type="checkbox" id="is_admin" name="is_admin" value="1" 
                                                        <?php echo (($staff['is_admin'] == 1) ? "checked" : ""); ?> 
                                                        <?php echo ($staff['id'] == 1 || $staff['id'] == $GLOBALS['user_details']['staff_id']) ? "disabled" : ""; ?>></td>
                                                </tr>
                                                <tr>
                                                    <th>Password: </th>
                                                    <td><input type="password" id="password" name="password" maxlength="255" value=""> (Ignore this if password does not need to be updated)</td>
                                                </tr>
                                            </table>
                                            <input type="submit" value="Update Details">
                                        </form>
                                        

                                        <form action='./php/admin_exec.php?type=staff&action=delete' method='post' onsubmit='return confirm("Delete this staff member?");'>
                                            <input type='hidden' id='id' name='id' value='<?php echo $staff['id']; ?>'>
                                            <input type='submit' value='Delete' <?php echo ($staff['id'] == 1 || $staff['id'] == $GLOBALS['user_details']['staff_id']) ? "disabled" : ""; ?>>
                                        </form>
                                    </td>
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