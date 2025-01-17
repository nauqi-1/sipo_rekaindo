
        document.addEventListener("DOMContentLoaded", () => {
            const editButton = document.getElementById("edit-button");
            const cancelButton = document.getElementById("cancel-button");
            const saveButton = document.getElementById("save-button");
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"]');
            const photoInput = document.getElementById("company-photo");
            const photoPreview = document.getElementById("company-photo-preview");

            // Set gambar default
            const defaultImage = "/img/setting/question.png"; // Path gambar default
            photoPreview.src = defaultImage; // Set gambar default awal

            // Disable all inputs at the start
            inputs.forEach(input => input.disabled = true);
            photoInput.disabled = true;

            // Ketika tombol Edit ditekan
            editButton.addEventListener("click", () => {
                inputs.forEach(input => input.disabled = false);
                photoInput.disabled = false;
                editButton.classList.add("d-none");
                cancelButton.classList.remove("d-none");
                saveButton.classList.remove("d-none");
            });

            // Ketika tombol Cancel ditekan
            cancelButton.addEventListener("click", () => {
                inputs.forEach(input => input.disabled = true);
                photoInput.disabled = true;
                editButton.classList.remove("d-none");
                cancelButton.classList.add("d-none");
                saveButton.classList.add("d-none");
            });

            // Ketika tombol Save Changes ditekan
            saveButton.addEventListener("click", () => {
                inputs.forEach(input => input.disabled = true);
                photoInput.disabled = true;
                editButton.classList.remove("d-none");
                cancelButton.classList.add("d-none");
                saveButton.classList.add("d-none");
                console.log("Data telah disimpan");
            });

            // Menangani perubahan gambar
            photoInput.addEventListener("change", (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        photoPreview.src = e.target.result; // Update dengan gambar baru
                    };
                    reader.readAsDataURL(file); // Baca file gambar
                } else {
                    // Jika tidak ada file, tampilkan gambar default
                    photoPreview.src = defaultImage;
                }
            });
        });
