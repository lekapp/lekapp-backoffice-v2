<div class="container d-flex align-items-center">
    <div class="form-holder has-shadow">
        <div class="row">
            <!-- Logo & Information Panel-->
            <div class="col-lg-6">
                <div class="info d-flex align-items-center justify-content-center">
                    <div class="logo m-2">
                        <img src="<?php echo asset_img('logo.jpg') ?>" width="100%">
                    </div>
                </div>
            </div>
            <!-- Form Panel    -->
            <div class="col-lg-6 bg-white">
                <div class="form d-flex align-items-center">
                    <div class="content">

                        <?php
                        $attr = array(
                            'method' => 'post',
                            'id' => 'login-form'
                        );
                        echo form_open('login', $attr);
                        ?>

                        <div class="form-group">
                            <!--<input id="login-username" type="text" name="loginUsername" required="" class="input-material">-->
                            <?php
                            $attr = array(
                                'id' => 'login-username',
                                'class' => 'input-material',
                                'name' => 'email',
                                'value' => set_value('email')
                            );
                            echo form_input($attr);
                            echo form_error('email');
                            ?>
                            <label for="login-username" class="label-material" required="">Nombre de usuario</label>
                        </div>
                        <div class="form-group">
                            <!--<input id="login-password" type="password" name="loginPassword" required="" class="input-material">-->
                            <?php
                            $attr = array(
                                'id' => 'login-password',
                                'class' => 'input-material',
                                'name' => 'password'
                            );
                            echo form_password($attr);
                            echo form_error('password');
                            ?>
                            <label for="login-password" class="label-material" required="">Contraseña</label>
                        </div>

                        <!--<a id="login" href="index.html" class="btn btn-primary">Login</a>-->
                        <?php
                        $attr = array(
                            'class' => 'btn btn-primary'
                        );
                        echo form_submit('acceder', 'Entrar', $attr);
                        ?>
                        <!-- This should be submit button but I replaced it with <a> for demo purposes-->

                        <?php echo form_close() ?>

                        <br>

                        <a href="#" class="forgot-pass">¿Olvidaste la contraseña?</a><br><small>¿No tienes cuenta?
                        </small><a href="<?= base_url('profile/add') ?>" class="signup">Regístrate</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>