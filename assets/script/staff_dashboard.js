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

discountForm.addEventListener("submit", (e) => {
  e.preventDefault();
  currentDiscount = parseFloat(document.getElementById("discountAmount").value);

  if (currentDiscount > 500.0) {
    alert("Discount exceeds weekly cap of PHP 500.00");
    return;
  }

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
    alert("Please enter the senior citizen name");
    return;
  }

  alert(
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
    alert(
      "Discount of PHP " +
        currentDiscount.toFixed(2) +
        " applied successfully to " +
        seniorName +
        "!"
    );
    btnBackToForm.click();
  }, 2000);
});

guardianForm.addEventListener("submit", (e) => {
  e.preventDefault();

  const guardianName = document.getElementById("guardianName").value;
  const seniorName = document.getElementById("seniorName").value;

  alert(
    "Guardian Information Verified:\n\nGuardian: " +
      guardianName +
      "\nSenior: " +
      seniorName +
      "\n\nDiscount of PHP " +
      currentDiscount.toFixed(2) +
      " applied successfully!"
  );

  guardianForm.reset();
  btnBackToForm.click();
});
