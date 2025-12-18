document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.lesson-container');
    // We look for sections inside the container that we will treat as steps
    const steps = document.querySelectorAll('.lesson-container > section');
    
    if (steps.length === 0 || !container) return;

    // Add class lesson-step to all sections
    steps.forEach(step => step.classList.add('lesson-step'));

    // Create Navigation UI
    const navDiv = document.createElement('div');
    navDiv.className = 'lesson-navigation';
    
    const prevBtn = document.createElement('button');
    prevBtn.className = 'nav-btn';
    prevBtn.innerText = 'Précédent';
    
    const nextBtn = document.createElement('button');
    nextBtn.className = 'nav-btn';
    nextBtn.innerText = 'Suivant';
    
    // Create Dots
    const dotsDiv = document.createElement('div');
    dotsDiv.className = 'progress-dots';
    steps.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.className = 'dot';
        dot.dataset.index = index;
        dotsDiv.appendChild(dot);
    });
    
    // Assemble
    navDiv.appendChild(prevBtn);
    navDiv.appendChild(dotsDiv);
    navDiv.appendChild(nextBtn);
    
    container.appendChild(navDiv);

    let currentStep = 0;

    function updateStep() {
        // Show/Hide Steps
        steps.forEach((step, index) => {
            if (index === currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        // Update Buttons
        prevBtn.disabled = currentStep === 0;
        if (currentStep === steps.length - 1) {
            nextBtn.innerText = 'Terminer';
        } else {
            nextBtn.innerText = 'Suivant';
        }

        // Update Dots
        const dots = dotsDiv.querySelectorAll('.dot');
        dots.forEach((dot, index) => {
            dot.classList.remove('active', 'completed');
            if (index === currentStep) {
                dot.classList.add('active');
            } else if (index < currentStep) {
                dot.classList.add('completed');
            }
        });
        
        // Scroll to top of container
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    prevBtn.addEventListener('click', () => {
        if (currentStep > 0) {
            currentStep--;
            updateStep();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentStep < steps.length - 1) {
            currentStep++;
            updateStep();
        } else {
            // Finish action - Redirect to home or course list
            window.location.href = '/'; 
        }
    });

    // Initialize
    updateStep();
});
