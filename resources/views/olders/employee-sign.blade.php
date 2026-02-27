<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Non-Disclosure Agreement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f3f3;
            color: #333;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .important {
            color: red;
        }


        .full-size-canvas {
            display: block; /* Remove inline-block spacing issues */
            width: 10%; /* Fill the width of the parent container */
            height: auto; /* Let the height adjust automatically based on the aspect ratio */
            margin: 0 auto; /* Center the canvas if necessary */
            /* background: red; */
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header" style="padding:1px;margin:1px">
            <table style="border:1px solid black !important;margin:4px !important">
                <tr>
                    <td style="width: 25%; text-align: center; vertical-align: middle;">
                        <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-optimized.png') }}"
                            alt="Neguinho Motors" width="70%" style="vertical-align: middle;">
                    </td>
                    <td style="width: 40%; text-align: left; vertical-align: middle;">
                        <div class="address">
                            <p style="font-size: 11px;"> 
                            9-13 Catford Hill<br>
                            London, SE6 4NU<br>
                            0203 409 5478 / 0208 314 1498<br>
                            customerservice@neguinhomotors.co.uk<br>
                            ngnmotors.co.uk
                        </div>
                    </td>
                    <td style="width: 35%; text-align: left; vertical-align: middle; font-weight: bold;">
                        <div class="title">Non-Disclosure Agreement</div>
                    </td>
                </tr>
            </table>
        </div>

        <form action="/signed/emp-nda/signed" method="POST">
            @csrf
            <h3 style='text-align:center; font-family: Arial, Helvetica, sans-serif'>Non-Disclosure Agreement (NDA) with
                Enforceability Clause</h3>
            <p>This Non-Disclosure Agreement (the "Agreement") is entered into on
                {{ \Carbon\Carbon::now()->addMinutes(5)->format('d F Y') }} by and between:</p>
            <p><strong>Neguinho Motors Ltd</strong><br>
                Registered Office: 9-13 Catford Hill, London, SE6 4NU<br>
                Company Registration Number: 11600635<br>
                (Hereinafter referred to as the "Company")</p>

            <p>AND</p>

            <div style="font-family: Arial, sans-serif; margin-top: 20px;">
                <label for="employeeName"
                    style="display: block; font-weight: bold; margin-bottom: 5px;">Employee:</label>
                <input type="text" id="employeeName" name="employeeName" style="height: 30px; width: 300px;"
                    required>

                <label for="email" style="display: block; font-weight: bold; margin-bottom: 5px;">Email:</label>
                <input type="email" id="email" name="email" style="height: 30px; width: 300px;"
                    value="{{ request()->query('email') }}" required>
                <span style="font-size: 11px; font-style: italic; color: darkgray; padding-left: 4px">your email
                    address</span>
                <br>

                <span style="font-size: 11px; font-style: italic; color: darkgray; padding-left: 4px">your name</span>
                <br>
                <label for="address" style="display: block; font-weight: bold; margin-bottom: 5px;">Address:</label>
                <input type="text" id="address" name='address' style="height: 30px; width: 400px;" required>
                <span style="font-size: 10px; font-style: italic; color: darkgray; padding-left: 4px">
                    your complete address (Example: 2000 TANSFELD ROAD, SE275DF, LONDON)
                </span>
                <p>(Hereinafter referred to as the "Employee")</p>

                <label for="date" style="display: block; font-weight: bold; margin-bottom: 5px;">Join Date:</label>
                <input type="date" id="date" name="date" style="height: 30px; width: 200px;" required>

            </div>
            <br>
            <h3>1. Purpose</h3>
            <p>The purpose of this Agreement is to protect the confidential information and trade secrets of Neguinho
                Motors
                Ltd. (the "Company") to which the Employee may be exposed during the course of their employment. The
                Employee acknowledges that they may have access to information that is proprietary and confidential to
                the
                Company and agrees to protect such information in accordance with the terms of this Agreement.</p>

            <h3>2. Confidential Information</h3>
            <p>For the purposes of this Agreement, "Confidential Information" shall include, but not be limited to:</p>
            <ul>
                <li style="padding:-10px !important">Any business, financial, technical, or operational information
                    relating to the
                    Company, its
                    products,
                    services, strategies, or customers.</li>
                <li>Any information related to the Company's profits, revenue, costs, expenses, and other financial
                    details.
                </li>
                <li>Any information related to the development, design, manufacture, or marketing of the Company's
                    products
                    and services.</li>
                <li>Trade secrets, proprietary information, and any other information which may not be publicly
                    available.
                </li>
                <li>Any information regarding the Company's business plans, strategies, forecasts, or other
                    forward-looking
                    information.</li>
                <li>Any personal data or other sensitive information concerning employees, customers, or other
                    stakeholders
                    of the Company.</li>
                <li>Any other information disclosed by the Company, whether disclosed in writing, orally, or by any
                    other
                    means, which is marked as confidential, or which would reasonably be understood to be confidential.
                </li>
            </ul>

            <h3>3. Obligations of the Employee</h3>
            <p>The Employee agrees:</p>
            <ul>
                <li>Not to disclose, discuss, or reveal any Confidential Information to any third party without the
                    prior
                    written consent of the Company.</li>
                <li>Not to use any Confidential Information for any purpose other than for the performance of their
                    duties
                    for the Company.</li>
                <li>To take all necessary precautions to protect the confidentiality of the Confidential Information.
                </li>
                <li>To immediately notify the Company of any potential or actual unauthorized disclosure of Confidential
                    Information.</li>
                <li>Not to discuss any matters directly or indirectly affecting the Company, including but not limited
                    to
                    matters related to profits, products, business operations, strategies, or any other business
                    interests
                    of the Company, with any third parties.</li>
            </ul>

            <h3>4. Enforceability and Remedies</h3>
            <h4>4.1 Legal Enforcement</h4>
            <p>The Employee acknowledges that this Agreement is enforceable under UK law. The Employee agrees that any
                breach of this Agreement may result in the Company pursuing legal action, including but not limited to,
                seeking damages, injunctions, and any other appropriate remedies available under the law.</p>
            <h4>4.2 Police Involvement</h4>
            <p>The Employee acknowledges that in the event of a breach of this Agreement, particularly where such breach
                involves the unlawful disclosure of confidential information, trade secrets, or other proprietary
                information, the Company may report such breach to the relevant authorities, including the police. The
                Employee understands that such a breach could result in criminal investigation and prosecution under
                applicable laws.</p>
            <h4>4.3 Court Orders and Injunctions</h4>
            <p>The Employee agrees that the Company shall be entitled to seek court orders, including injunctions, to
                prevent or restrain any unauthorized use or disclosure of Confidential Information. The Employee agrees
                that
                any court of competent jurisdiction may enforce such orders.</p>
            <h4>4.4 Indemnity</h4>
            <p>The Employee agrees to indemnify and hold harmless the Company against any losses, damages, liabilities,
                costs, and expenses (including legal fees) incurred as a result of any breach of this Agreement by the
                Employee.</p>

            <h3>5. Return of Materials</h3>
            <p>Upon termination of employment or at the request of the Company, the Employee agrees to return all
                materials
                containing Confidential Information in their possession, including but not limited to documents, notes,
                files, and any copies thereof.</p>

            <h3>6. Duration</h3>
            <p>The obligations of the Employee under this Agreement shall continue:</p>
            <ul>
                <li>Indefinitely, for any Confidential Information that constitutes a trade secret under applicable law.
                </li>
                <li>For a period of [2/3/5] years from the date of termination of the Employee's employment with the
                    Company, for all other Confidential Information.</li>
            </ul>


            <h3>7. Governing Law and Jurisdiction</h3>
            <p>This Agreement shall be governed by and construed in accordance with the laws of England and Wales. The
                Employee agrees that any disputes arising under or in connection with this Agreement shall be subject to
                the
                exclusive jurisdiction of the courts of England and Wales.</p>

            <h3>8. Entire Agreement</h3>
            <p>This Agreement constitutes the entire agreement between the parties with respect to the subject matter
                hereof
                and supersedes all prior agreements, understandings, and communications, whether written or oral,
                relating
                to such subject matter.</p>

            <h3>9. Amendment and Waiver</h3>
            <p>No amendment or waiver of any provision of this Agreement shall be effective unless it is in writing and
                signed by both parties. No failure or delay by the Company in exercising any right under this Agreement
                shall constitute a waiver of that right.</p>

            <h3>10. Severability</h3>
            <p>If any provision of this Agreement is found to be invalid or unenforceable, the remaining provisions
                shall
                remain in full force and effect, and the invalid or unenforceable provision shall be modified to the
                minimum
                extent necessary to make it valid and enforceable.</p>

            <h3>11. Intellectual Property Rights</h3>
            <p>You acknowledge that:</p>
            <ul>
                <li>A. all Employment IPRs, Employment Inventions and works embodying them shall be owned automatically and absolutely by the Employer to the fullest extent permitted by law. To the extent that they are not automatically owned by the Employer, you hold them on trust for us; and</li>
                <li>B. because of the nature of your duties and the particular responsibilities arising from the nature of your duties, you have, and shall have at all times while you are employed by us, a special obligation to further the interests of the Employer.</li>
            </ul>

            <h3>12. Obligations Regarding Employment Inventions</h3>
            <p>You agree:</p>
            <ul>
                <li>A. to promptly and on their creation, give us full written details of all Employment Inventions you make wholly or partially during the course of your employment;</li>
                <li>B. at the Company's request, and in any event, on the termination of your employment, to give us all originals and copies of correspondence, documents, papers and records on all media which record or relate to any of the Employment IPRs;</li>
                <li>C. to use your best endeavours to execute all documents and do all acts both during and after your employment by us as may, in the opinion of the Employer, be necessary or desirable to vest the Employment IPRs in the Employer, to register them in the name of the Employer and to protect and maintain the Employment IPRs and the Employment Inventions;</li>
                <li>D. to give us all necessary assistance to enable us to enforce the Company's Intellectual Property Rights against third parties, to defend claims for infringement of third party Intellectual Property Rights and to apply for registration of Intellectual Property Rights, where appropriate throughout the world, and for the full term of those rights;</li>
                <li>E. not to attempt to register any Employment IPR nor patent any Employment Invention unless the Company requests that you do so; and</li>
                <li>F. to keep confidential each Employment Invention unless the Company has consented to its disclosure in writing.</li>
            </ul>

            <h3>13. Waiver of Moral Rights</h3>
            <p>You waive all moral rights under the Copyright, Designs and Patents Act 1988 (and all similar rights in other jurisdictions) which you have or will have in any existing or future works.</p>

            <h3>14. Attorney Appointment</h3>
            <p>You hereby irrevocably appoint the Employer to be your attorney in your name and on your behalf to execute documents, use your name and do all things which are necessary or desirable for the Employer to obtain for itself or its nominee the full benefit of this section.</p>

            <h3>15. Employer's Procedures</h3>
            <p>The Company has various policies, rules and procedures which will be applicable to your employment. You are expected to act in accordance with such policies and rules and to follow procedures. Except where otherwise stated, any material contained in statements of the policies, rules and procedures does not form part of your terms and conditions of employment. The Company may make additions, deletions or variations to its policies, rules and procedures from time to time.</p>

            <h3>16. Disciplinary and Grievance Procedures</h3>
            <p>If you are dissatisfied with any disciplinary decision relating to you (including any decision to dismiss you) then you should notify your line manager in writing, specifying the grounds for your dissatisfaction. Further information can be found in the Disciplinary Procedure.</p>
            <p>If you wish to seek redress for any grievance relating to your employment then you should notify your immediate supervisor in writing, specifying the grounds for your grievance. If your grievance relates to your immediate supervisor then you can instead notify an alternative director. Further information can be found in the Grievance Procedure.</p>

            <h3>17. Electronic Communications</h3>
            <p>Without prejudice to any other rights it may have, the Company reserves the right to intercept and/or monitor and/or record and/or view, as appropriate, your use of its electronic communication systems including telephone, pc, remote access via a laptop or other means for the purpose of ensuring that its systems:</p>
            <ul>
                <li>A. are used primarily to further the business of the Company;</li>
                <li>B. are not used for inappropriate or unlawful purposes, such as accessing or circulating material containing nudity, pornography, racist terminology or other offensive material or for telephoning premium-rate lines;</li>
                <li>C. are used in compliance with the relevant policies and procedures of the Company;</li>
                <li>D. have sufficient capacity for the needs of the business.</li>
            </ul>
            <p>By signing this contract you understand and agree that the content of your communications and access to the Company's systems can be accessed and hereby consent to any such interception, monitoring, viewing and/or recording taking place.</p>

            <h3>18. Collective Agreements</h3>
            <p>There are no collective agreements affecting your terms and conditions of employment.</p>

            <h3>19. Amendment to Terms and Conditions</h3>
            <p>The Company is entitled to make reasonable changes to any of the terms of your employment as it deems appropriate. The Company will notify you in writing of any change before the date it comes into force.</p>

            <h3>20. Prevention of Unfair Competition</h3>
            <p>In this clause, "Restricted Client" means any client of the Company or any business in which the Company has a shareholding with whom you have had dealings while in employment, within a period of 12 months preceding the date of termination of your employment. "Restricted Employee" means any senior or professional employee of the Company of Manager level or above with whom you worked in the 12 months preceding the date of termination of your employment (whether alone or in conjunction with others who are members of a team in which you worked) where the departure of that employee is intended to be for your benefit, or the benefit of your new employer or any other organisation carrying out business in competition with the Company.</p>
            <p>You hereby agree that for a period of 12 months following the date of termination of your employment you shall not without the written consent of the Company (such consent not to be unreasonably withheld) in the United Kingdom:</p>
            <ul>
                <li>A. Render services similar to those services offered by the Company of a kind performed by you during the 12 months preceding the date of termination of your employment to any Restricted Client except where such services are rendered as an employee of the Restricted Client;</li>
                <li>B. Solicit the business of, or endeavour to solicit the business of, any Restricted Client in competition with the business of or services offered by the Company;</li>
                <li>C. Solicit, entice away, or endeavour to entice away or assist any third party to solicit, entice away or endeavour to entice away from the Company, any Restricted Employee.</li>
                <li>D. The above provisions are considered reasonable and necessary to protect legitimate business interests of the Company.</li>
            </ul>

            <h3>21. Governing Law</h3>
            <p>This Agreement shall be governed by and interpreted according to the law of England and Wales and all disputes arising under the Agreement (including non-contractual disputes or claims) shall be subject to the exclusive jurisdiction of the English and Welsh courts.</p>

            <h3>22. Third Party Rights</h3>
            <p>No provision of this contract shall be enforceable by any person who is not a party to it pursuant to the Contracts (Rights of Third Parties) Act 1999.</p>
            
            <p>IN WITNESS WHEREOF, the parties have executed this Agreement as of the day and year first above written.
            </p>
            
            <p>Employee Name: Muhammad Shariq Ayaz</p>
            <p>Date: </p>
            <p>Neguinho Motors Ltd</p>
            <p>Date: </p>

            <p>This Non-Disclosure Agreement is designed to be legally binding and enforceable under UK law, including
                provisions that allow for police involvement and court enforcement in cases of breach.</p>
    </div>


    <p style="text-align: center; color: red; font-weight:bolder; font-size:18px">Kindly, send your passport or Licence
        illustrating
        signature and your details to:
        thiago@neguinhomotors.co.uk</p>
    <p style="text-align: center; color: red; font-weight: bolder; font-size: 18px;">
        Por favor, envie seu passaporte ou carteira de motorista
        que mostre a assinatura e seus detalhes para:
        thiago@neguinhomotors.co.uk
    </p>

    <div class="text-center">
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#signatureModal">
            Sign Here!
        </button>
    </div>
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content text-center">
                <p class="mt-3 text-white" id="success-message"></p>
                <div id="signature-pad-booking-id">
                    <input type="hidden" name="booking_id" value="1">
                </div>
                <div id="sigpad" style="width: 100%; height: calc(100vh - 56px);text-align:center;"> <!-- Adjusting height to account for close button -->
                    <x-creagia-signature-pad class="kbw-signature" style="width: 100%; height: 100%;"
                        border-color="#eaeaea" pad-classes="rounded-xl border-2"
                        button-classes="ngn-bg px-4 py-2 mt-4" clear-name="Clear"  submit-name="Submit" />
                        <button type="button" class="btn btn-danger " data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="text-center">
        <i class="dripicons-checkmark h1 text-white"></i>
        <h4 class="mt-2 text-black">Sign Here!</h4>
        <p class="mt-3 text-white" id="success-message"></p>
        <div id="signature-pad-booking-id">
            <input type="hidden" name="booking_id" value="1">
        </div>
        <div style="text-align: center;" id="sigpad">
            <x-creagia-signature-pad class="kbw-signature" style="color: white;width:100%; height:400px"
                border-color="#eaeaea" pad-classes="rounded-xl border-2"
                button-classes="ngn-bg px-4 py-2 mt-4" clear-name="Clear" submit-name="Submit" />
        </div>

    </div> --}}
    </form>

    </div>

    <script src="{{ asset('assets/js/sign-pad.min.js') }}"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
    // Function to resize the canvas
    function resizeCanvas() {
        const canvas = document.querySelector("canvas");
        if (canvas) {
            // Remove any previously set width and height attributes
            canvas.removeAttribute("width");
            canvas.removeAttribute("height");

            // Set the width to 100% of the parent container
            const containerWidth = canvas.parentElement.offsetWidth;
            const newWidth = containerWidth * 0.95; // 90% of the container width
            const newHeight = newWidth / 2.8; // Maintain 2:1 aspect ratio

            // Apply the new width and height
            canvas.style.width = `${newWidth}px`;
            canvas.style.height = `${newHeight}px`;

            // Adjust internal canvas resolution for high DPI screens (e.g., Retina)
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = newWidth * ratio;
            canvas.height = newHeight * ratio;

            // Get the 2D drawing context and scale for high DPI
            const ctx = canvas.getContext("2d");
            ctx.scale(ratio, ratio);

            // Clear the canvas to ensure proper scaling
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    // Resize the canvas when the page is loaded
    resizeCanvas();

    // Re-resize the canvas when the window is resized
    window.addEventListener("resize", resizeCanvas);

    // Optional: If the canvas is inside a modal or similar, resize after it's shown
    const signatureModal = document.getElementById("signatureModal");
    if (signatureModal) {
        signatureModal.addEventListener("shown.bs.modal", resizeCanvas);
    }

    // Add the full-size-canvas class to the canvas element for styling
    const canvas = document.querySelector("canvas");
    if (canvas) {
        canvas.classList.add("full-size-canvas");
    }
});
        $(document).ready(function() {
            $('#signature-pad-cancel').click(function() {
                $('#signature-pad-booking-id').empty();
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            // Assuming 'signaturePad' is your signature pad instance,
            // you may need to obtain it from the 'x-creagia-signature-pad' component
            var signaturePad;

            var form = document.querySelector("form");
            form.addEventListener("submit", function(event) {
                if (signaturePad && signaturePad.isEmpty()) {
                    event.preventDefault(); // Prevent form submission
                    alert('Please provide a signature.'); // Inform the user
                    // Or update the content of a <p> element with your error message
                }

                // Check if any input fields are empty
                var inputs = form.querySelectorAll("input");
                var emptyInputs = Array.from(inputs).filter(function(input) {
                    return input.value.trim() === "";
                });

                if (emptyInputs.length > 0) {
                    event.preventDefault(); // Prevent form submission
                    alert('Please fill in all required fields.'); // Inform the user
                    // Or update the content of a <p> element with your error message
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
