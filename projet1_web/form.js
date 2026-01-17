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
    
    // Height slider update
    heightSlider.addEventListener('input', function() {
        heightValue.textContent = this.value + ' cm';
    });
    
    // Salary slider update
    salarySlider.addEventListener('input', function() {
        salaryValue.textContent = parseInt(this.value).toLocaleString() + ' €';
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
            heightValue.textContent = '170 cm';
            salaryValue.textContent = '10000 €';
        }
    });
    
    // Set initial empty state for image preview
    imagePreview.classList.add('empty');
});

// Optional: Add animation when form sections come into view
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '0';
            entry.target.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                entry.target.style.transition = 'all 0.5s ease';
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, 100);
            
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe all form sections
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.form-section');
    sections.forEach(section => observer.observe(section));
});