
<div class="adminHeaderTop">
  <div class="maincontent">
    <div class="logo"><a href="<?php echo base_url();?>" title="<?php echo SITE_NAME; ?>"><img src="<?php echo base_url(); ?>images/logo.png" border="0" alt="<?php echo SITE_NAME; ?> Logo"> </a></div>
    <div class="navigation">
      <nav id="nav" role="navigation">
        <?php  $this->load->view('includes/main-menu'); ?>
      </nav>
    </div>
  </div>
</div>
<div id="adminheadcontainer">
  <!--header container start-->
  <div class="maincontent">
    <header>
      <div class="section group">
        <!--content of home page div-->
        <div class="col span_1_of_4"><img src="<?php echo base_url(); ?>images/clr.gif" border="0" alt=""></div>
        <div class="col span_3_of_4">
          <div class=" navigation">
            <nav id="nav" role="navigation"> <a href="#nav" title="Show navigation">Show navigation</a> <a href="#" title="Hide navigation">Hide navigation</a>
              <ul id="top_menu" class="clearfix">
                <?php if(is_logged_in()):?>
                <?php if(is_allowed('edu_admin/home/index')): ?>
                <li>
                  <?php  echo anchor('edu_admin','Dashboard'); ?>
                  <ul class="sub-menu">
                    <?php if(is_allowed('edu_admin/page/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/page','Manage Content'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/text/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/text/','Manage Text'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/slide/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/slide','Manage Slides'); ?>
                    </li>
                    <?php endif; ?>
                    <!--Janet-->
                    <?php if(is_allowed('edu_admin/faq/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/faq','Manage FAQs'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/email_template/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/email_template','Manage Emails '); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/news/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/news','Manage News'); ?>
                    </li>
                    <?php endif; ?>
                    <!--end  Janet-->
                  </ul>
                </li>
                <?php endif; ?>
                <?php if(is_allowed('edu_admin/home/index')): ?>
                <li class="navmanage"> <a href="#">
                  <?php  echo 'Manage'; ?>
                  </a>
                  <ul class="sub-menu">
                    <!--Janet-->
                    <?php if(is_allowed('edu_admin/questionnaire/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/questionnaire','Manage Questionnaire'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/questionnaire_report/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/questionnaire_report/','Questionnaire Reports'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/testimonials/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/testimonials/','Update Testimonials'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/assignment/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/assignment','Manage Assignment'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/location/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/location','Manage District '); ?>
                    </li>
                    <li>
                      <?php  echo anchor('edu_admin/location/iu_unit','Manage IU '); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/user/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/user','Manage Users'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/contact/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/contact','Manage Contacts'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/newsletter/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/newsletter','Manage Newsletter Subscription'); ?>
                    </li>
                    <li>
                      <?php  echo anchor('edu_admin/newsletter/unsubscribe','Newsletter Unsubscription'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/permission/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/permission','Manage Permissions'); ?>
                    </li>
                    <?php endif; ?>
                    <!--end  Janet-->
                  </ul>
                </li>
                <?php endif; ?>
                <?php if(is_allowed('edu_admin/home/index')): ?>
                <li class="navcoursesetup"> <a href="#">Courses Setup</a>
                  <ul>
                    <?php if(is_allowed('edu_admin/course_genres/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/course_genres','Manage Course Genres'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/course_definition/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/course_definition','Manage Course Definition'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/course_schedule/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/course_schedule','Manage Course Schedule'); ?>
                    </li>
                    <?php endif; ?>
                    <!--Janet-->
                    <?php if(is_allowed('edu_admin/course_schedule/one_credit')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/course_schedule/one_credit','Manage Course Schedule(One Credit)'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/course_definition/sessions')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/course_definition/sessions','Manage Course Session'); ?>
                    </li>
                    <?php endif; ?>
					
					<?php if(is_allowed('edu_admin/course_schedule/enrollees')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/course/enrollees','Course Enrollees'); ?>
                    </li>
					<?php endif; ?>
					<?php if(is_allowed('edu_admin/course_schedule/enrollees')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/course/registrants','Course Registrants'); ?>
                    </li>
					<?php endif; ?>
                    <?php if(is_allowed('edu_admin/course_reservation/course_unregistrant')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/course_reservation/course_unregistrant','Course Unregistrant'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/course_reservation/course_unenrollees')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/course_reservation/course_unenrollees/','Course Unenrollees'); ?>
                    </li>
                    <?php endif; ?>
                    <!--end  Janet-->
                  </ul>
                </li>
                <?php endif; ?>
                <?php if(INSTRUCTOR == $this->session->userdata('access_level')): ?>
                <li> <a href="<?php echo base_url() ?>instructor/">Dashboard</a>
                  <ul class="sub-menu">
                    <li>
                      <?php  echo anchor('user/change_password','Change Password'); ?>
                    </li>
                    <li> <a href="<?php echo base_url() ?>testimonials/instructor_approval/">Approve Testimonials</a> </li>
                    <?php if(is_allowed('questionnaire_report/index')): ?>
                    <li>
                      <?php  echo anchor('questionnaire_report/index/'.$this->session->userdata('user_id'),'Questionnaire Reports'); ?>
                    </li>
                    <?php endif; ?>
                  </ul>
                </li>
                <li> <a href="<?php echo base_url() ?>user/">Member Directory</a> </li>
                <?php elseif(MEMBER == $this->session->userdata('access_level')): ?>
                <li> <a href="<?php echo base_url() ?>member/">Dashboard</a> </li>
                <?php				if(isset($pay_for_course)):				echo '<li>'.anchor('checkout/','Pay for Course','class="editButton"').'</li>'; 				endif;				?>
                <li>
                  <?php  echo anchor('user/change_password','Change Password'); ?>
                </li>
                <?php  endif;?>
                <?php 			if("" !=  $this->session->userdata('emulate')): ?>
                <li> <?php echo anchor('user/switch_admin','Switch to Admin'); ?></li>
                <?php endif; ?>
                <?php endif; ?>
                <?php if(is_allowed('edu_admin/inventory/index')): ?>
                <li><?php echo anchor('edu_admin/inventory/index','Store') ?>
                  <!--Janet-->
                  <ul class="sub-menu">
                    <?php if(is_allowed('edu_admin/order/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/order','Manage Order : Ipads'); ?>
                    </li>
                    <?php endif; ?>
                    <?php if(is_allowed('edu_admin/transaction/index')): ?>
                    <li>
                      <?php  echo anchor('edu_admin/transaction','Manage Transactions'); ?>
                    </li>
                    <?php endif; ?>
                  </ul>
                  <!--End Janet-->
                </li>
                <?php  endif; ?>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </header>
  </div>
</div>
