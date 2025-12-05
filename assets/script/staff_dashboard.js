const discountForm = document.getElementById("discountForm");
const verificationSection = document.getElementById("verificationSection");
const btnSeniorVerify = document.getElementById("btnSeniorVerify");
const btnGuardianVerify = document.getElementById("btnGuardianVerify");
const btnBackToForm = document.getElementById("btnBackToForm");
const seniorVerificationSection = document.getElementById(
  "seniorVerificationSection"
);
const guardianVerificationSection = document.getElementById(
  "guardianVerificationSection"
);
const btnStartFingerprint = document.getElementById("btnStartFingerprint");
const guardianForm = document.getElementById("guardianForm");

let currentDiscount = 0;

// Modal utility function
function showModal(message, title = "Notification") {
  // Create modal elements
  const modal = document.createElement("div");
  modal.className = "modal";
  modal.innerHTML = `
    <div class="modal-content">
      <h3>${title}</h3>
      <p>${message.replace(/\n/g, '<br>')}</p>
      <div class="modal-actions">
        <button class="confirm">OK</button>
      </div>
    </div>
  `;

  // Append to body
  document.body.appendChild(modal);

  // Show modal
  modal.style.display = "flex";

  // Handle close
  const confirmBtn = modal.querySelector(".confirm");
  confirmBtn.addEventListener("click", () => {
    modal.remove();
  });

  // Close on background click
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.remove();
    }
  });
}

function refreshStats() {
  fetch('assets/backend/get_stats.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const stats = data.stats;
        document.querySelector('.stats-cards .card:nth-child(1) h3').textContent = 'PHP ' + stats.today_discount;
        document.querySelector('.stats-cards .card:nth-child(2) h3').textContent = 'PHP ' + stats.total_discount;
        document.querySelector('.stats-cards .card:nth-child(3) h3').textContent = stats.total_senior;
        document.querySelector('.stats-cards .card:nth-child(4) h3').textContent = stats.total_transactions;
      }
    })
    .catch(error => {
      console.error('Error refreshing stats:', error);
    });
}

discountForm.addEventListener("submit", (e) => {
  e.preventDefault();
  currentDiscount = parseFloat(document.getElementById("discountAmount").value);

  discountForm.parentElement.style.display = "none";
  verificationSection.style.display = "block";
});

btnSeniorVerify.addEventListener("click", () => {
  seniorVerificationSection.style.display = "block";
  guardianVerificationSection.style.display = "none";
});

btnGuardianVerify.addEventListener("click", () => {
  guardianVerificationSection.style.display = "block";
  seniorVerificationSection.style.display = "none";
});

btnBackToForm.addEventListener("click", () => {
  discountForm.parentElement.style.display = "block";
  verificationSection.style.display = "none";
  seniorVerificationSection.style.display = "none";
  guardianVerificationSection.style.display = "none";
  discountForm.reset();
});

btnStartFingerprint.addEventListener("click", () => {
  const seniorName = document.getElementById("seniorFullName").value;

  if (!seniorName.trim()) {
    showModal("Please enter the senior citizen name");
    return;
  }

  fetch('assets/backend/validate_discount.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      senior_name: seniorName,
      discount_amount: currentDiscount,
      verification_type: 'Fingerprint'
    })
  })
  .then(response => response.json())
  .then(data => {
    if (!data.success) {
      showModal(data.message);
      return;
    }

    showModal(
      "Fingerprint scan initiated. (Integration with biometric hardware needed)"
    );
    const resultDiv = document.getElementById("fingerprintResult");
    resultDiv.style.display = "block";
    resultDiv.className = "success";
    resultDiv.innerHTML =
      "<strong>✓ Fingerprint Verified</strong><br>Senior: " +
      seniorName +
      "<br>Discount: PHP " +
      currentDiscount.toFixed(2);

    setTimeout(() => {
      showModal(
        "Discount of PHP " +
          currentDiscount.toFixed(2) +
          " applied successfully to " +
          seniorName +
          "!"
      );

      fetch('assets/backend/log_discount_activity.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          senior_name: seniorName,
          discount_amount: currentDiscount,
          verification_type: 'Fingerprint'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (!data.success) {
          console.error('Failed to log activity:', data.message);
        } else {
          refreshStats();
          refreshLogs();
        }
      })
      .catch(error => {
        console.error('Error logging activity:', error);
      });

      btnBackToForm.click();
    }, 2000);
  })
  .catch(error => {
    console.error('Error validating senior:', error);
    showModal('Error validating senior. Please try again.');
  });
});

guardianForm.addEventListener("submit", (e) => {
  e.preventDefault();

  const guardianName = document.getElementById("guardianName").value;
  const seniorName = document.getElementById("seniorName").value;

  fetch('assets/backend/validate_discount.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
      senior_name: seniorName,
      guardian_name: guardianName,
      verification_type: 'Guardian'
    })
  })
  .then(response => response.json())
  .then(data => {
    if (!data.success) {
      showModal(data.message);
      return;
    }

    showModal(
      "Guardian Information Verified:\n\nGuardian: " +
        guardianName +
        "\nSenior: " +
        seniorName +
        "\n\nDiscount of PHP " +
        currentDiscount.toFixed(2) +
        " applied successfully!"
    );

    fetch('assets/backend/log_discount_activity.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        senior_name: seniorName,
        discount_amount: currentDiscount,
        verification_type: 'Guardian Verification'
      })
    })
    .then(response => response.json())
    .then(data => {
      if (!data.success) {
        console.error('Failed to log activity:', data.message);
      } else {
        refreshStats();
        refreshLogs();
      }
    })
    .catch(error => {
      console.error('Error logging activity:', error);
    });

    guardianForm.reset();
    btnBackToForm.click();
  })
  .catch(error => {
    console.error('Error validating guardian:', error);
    showModal('Error validating guardian. Please try again.');
  });
});
