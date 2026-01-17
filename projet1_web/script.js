// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Get form elements
    const form = document.getElementById('userForm');
    const photoInput = document.getElementById('photo');
    const imagePreview = document.getElementById('imagePreview');
    const heightSlider = document.getElementById('height');
    const heightValue = document.getElementById('heightValue');
    const salarySlider = document.getElementById('salary');
    const salaryValue = document.getElementById('salaryValue');
    
    // Image preview functionality
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Check if file is an image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    imagePreview.classList.remove('empty');
                };
                
                reader.readAsDataURL(file);
            } else {
                alert('Veuillez sélectionner un fichier image valide.');
                photoInput.value = '';
            }
        } else {
            imagePreview.innerHTML = '';
            imagePreview.classList.add('empty');
        }
    });
    
    // Height slider 
    heightSlider.addEventListener('input', function(e) {
        heightValue.textContent = e.target.value + ' cm';
    });
    
    // Salary slider 
    salarySlider.addEventListener('input', function(e) {
        const value = parseInt(e.target.value);
        salaryValue.textContent = value.toLocaleString('fr-FR') + ' €';
    });
    
    // Form validation before submit
    form.addEventListener('submit', function(e) {
        // Check if at least one checkbox is selected
        const checkboxes = document.querySelectorAll('input[name="coordonnees[]"]:checked');
        
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une méthode de contact.');
            return false;
        }
        
        // Validate email format
        const email = document.getElementById('email').value;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailPattern.test(email)) {
            e.preventDefault();
            alert('Veuillez entrer une adresse email valide.');
            return false;
        }
        
        // Validate phone number (basic validation)
        const mobile = document.getElementById('mobile').value;
        if (mobile.length < 10) {
            e.preventDefault();
            alert('Veuillez entrer un numéro de téléphone valide.');
            return false;
        }
        
        // If all validations pass, show confirmation
        if (confirm('Êtes-vous sûr de vouloir envoyer ce formulaire ?')) {
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });
    
    // Reset button functionality with confirmation
    form.addEventListener('reset', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')) {
            e.preventDefault();
        } else {
            // Clear image preview
            imagePreview.innerHTML = '';
            imagePreview.classList.add('empty');
            
            // Reset slider values display
            setTimeout(function() {
                heightValue.textContent = '170 cm';
                salaryValue.textContent = '10 000 €';
            }, 10);
        }
    });
    
    // Set initial empty state for image preview
    imagePreview.classList.add('empty');
});