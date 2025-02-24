                    <form action="upload-profile.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="profile_picture">Foto de Perfil</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Nome</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($userData['name']); ?>" placeholder="Mantenha o nome atual">
                        </div>
                        <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                    </form> 