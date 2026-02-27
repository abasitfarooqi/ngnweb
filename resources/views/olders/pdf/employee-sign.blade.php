{{-- Finance Contract | 22 JULY 2024 V2 Update Rev.1 --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/colors/color1.css">
    {{-- bootstrap cdn --}}


    <!-- Favicon and touch icons  -->
    <link href="{{ url('/img/white-bg-ico.ico') }}" rel="shortcut icon">
    {{-- changed --}}

    <title>Employee NDA Agreement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 100%;
            padding: 0px;
            margin: 0px;
            font-size: 11px;
            background: url('{{ secure_asset('https://neguinhomotors.co.uk/img/watermark.png') }}');
            background-repeat: repeat;
            background-size: 1100px;
        }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 12px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }

        .logo {
            width: 150px;
        }

        .header .address,
        .header .title {
            text-align: left;
            flex: 1;
            padding: 0 20px;
            font-size: 10px;
        }

        .header .title {
            font-size: 16px;
            font-weight: bold;
        }

        .no-border td {
            border: none;
        }

        .container {
            padding: 20px;
        }

        .table-con {
            width: 100% !important;
            border-collapse: collapse;
            border: 0.4px black solid;
            border-bottom:0;
        }
        
        .bottom-border{
 
            border-bottom: 0.4px black solid;

        }

        th,
        td-cont {
            /* border: 0.4px dotted black; */
            border: none;
            padding: 10px;
            padding-left: 13px;
        }

        .attention {
            color: red;
            font-weight: bold;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }

        .left-padding {
            padding-left: 20px;
            margin: 0px;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .hr-line {
            width: 98%;
            border: none;
            border-top: 0.4px dotted black;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            font-size: 10px;
        }

        p {
            padding-top: 0px !important;
            margin: 0px !important;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header" style="margin:0;padding:0;padding-top:15px;">
            <table style="border:1px solid rgb(102, 102, 102) !important;margin:0;padding:0;">
                <tr>
                    <td style="width: 25%; text-align: center; vertical-align: middle;">
                        <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/ngn-motor-logo-fit-small.png') }}"
                            alt="Neguinho Motors" width="70%" style="vertical-align: middle;">
                    </td>
                    <td style="width: 40%; text-align: left; vertical-align: middle;">
                        <div class="address">
                            9-13 Catford Hill, <br>
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

        <h2 style="padding-top: 6px !important; text-align: center;margin:0;padding:0">Non-Disclosure Agreement (NDA)
            with Enforceability Clause</h2>
        <br>
        <p style="text-align: left;margin:0;padding:0">This Non-Disclosure Agreement (the "Agreement") is entered into
            on
            Date: <b>{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</b>
            by and between:</p>
        <br>
        <div style="font-family: Arial, sans-serif;padding-top:5px">
            <p style="padding-bottom:5px;"><strong>Neguinho Motors Ltd</strong><br>
                Registered Office: 9-13 Catford Hill, London, SE6 4NU<br>
                Company Registration Number: 11600635<br>
                (Hereinafter referred to as the "Company")</p>
        </div>

        <p style="font-weight: bold; padding-top:5px;">AND</p>

        <div style="font-family: Arial, sans-serif;padding-top:5px">
            <label for="employeeName" style="display: block; font-weight: bold; margin-bottom: 5px;">Employee:
                {{ $customer }}</label>
            <p>Address:
                {{ $address }}</p>

            <p>(Hereinafter referred to as the "Employee")</p>
        </div>

        <h3>1. Purpose</h3>
        <p>The purpose of this Agreement is to protect the confidential information and trade secrets of Neguinho
            Motors
            Ltd. (the "Company") to which the Employee may be exposed during the course of their employment. The
            Employee acknowledges that they may have access to information that is proprietary and confidential to
            the
            Company and agrees to protect such information in accordance with the terms of this Agreement.</p>

        <h3>2. Confidential Information</h3>
        <p>For the purposes of this Agreement, "Confidential Information" shall include, but not be limited to:</p>
        <ul style="margin:0;padding:0;">
            <li style="margin:0;padding:0;margin-left:10px;">Any business, financial, technical, or operational
                information relating to
                the Company, its
                products,
                services, strategies, or customers.</li>
            <li style="margin:0;padding:0;margin-left:10px;">Any information related to the Company's profits,
                revenue,
                costs, expenses,
                and other financial
                details.
            </li>
            <li style="margin:0;padding:0;margin-left:10px;">Any information related to the development, design,
                manufacture, or
                marketing of the Company's
                products
                and services.</li>
            <li style="margin:0;padding:0;margin-left:10px;">Trade secrets, proprietary information, and any other
                information which may
                not be publicly
                available.
            </li>
            <li style="margin:0;padding:0;margin-left:10px;">Any information regarding the Company's business plans,
                strategies,
                forecasts, or other
                forward-looking
                information.</li>
            <li style="margin:0;padding:0;margin-left:10px;">Any personal data or other sensitive information
                concerning
                employees,
                customers, or other
                stakeholders
                of the Company.</li>
            <li style="margin:0;padding:0;margin-left:10px;">Any other information disclosed by the Company, whether
                disclosed in
                writing, orally, or by any
                other
                means, which is marked as confidential, or which would reasonably be understood to be confidential.
            </li>
        </ul>

        <h3>3. Obligations of the Employee</h3>
        <p>The Employee agrees:</p>
        <ul style="margin:0;padding:0;">
            <li style="margin:0;padding:0;margin-left:10px;">Not to disclose, discuss, or reveal any Confidential
                Information to any
                third party without the
                prior
                written consent of the Company.</li>
            <li style="margin:0;padding:0;margin-left:10px;">Not to use any Confidential Information for any purpose
                other than for the
                performance of their
                duties
                for the Company.</li>
            <li style="margin:0;padding:0;margin-left:10px;">To take all necessary precautions to protect the
                confidentiality of the
                Confidential Information.
            </li>
            <li style="margin:0;padding:0;margin-left:10px;">To immediately notify the Company of any potential or
                actual unauthorized
                disclosure of Confidential
                Information.</li>
            <li style="margin:0;padding:0;margin-left:10px;">Not to discuss any matters directly or indirectly
                affecting
                the Company,
                including but not limited
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
        <ul style="margin:0;padding:0;">
            <li style="margin:0;padding:0;margin-left:10px;">Indefinitely, for any Confidential Information that
                constitutes a trade
                secret under applicable law.
            </li>
            <li style="margin:0;padding:0;margin-left:10px;">For a period of [2/3/5] years from the date of
                termination
                of the Employee's
                employment with the
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
        <ul style="margin:0;padding:0;">
            <li style="margin:0;padding:0;margin-left:10px;">A. all Employment IPRs, Employment Inventions and works embodying them shall be owned automatically and absolutely by the Employer to the fullest extent permitted by law. To the extent that they are not automatically owned by the Employer, you hold them on trust for us;</li>
            <li style="margin:0;padding:0;margin-left:10px;">B. because of the nature of your duties and the particular responsibilities arising from the nature of your duties, you have, and shall have at all times while you are employed by us, a special obligation to further the interests of the Employer.</li>
        </ul>

        <h3>12. Obligations Regarding Employment Inventions</h3>
        <p>You agree:</p>
        <ul style="margin:0;padding:0;">
            <li style="margin:0;padding:0;margin-left:10px;">A. to promptly and on their creation, give us full written details of all Employment Inventions you make wholly or partially during the course of your employment;</li>
            <li style="margin:0;padding:0;margin-left:10px;">B. at the Company's request, and in any event, on the termination of your employment, to give us all originals and copies of correspondence, documents, papers and records on all media which record or relate to any of the Employment IPRs;</li>
            <li style="margin:0;padding:0;margin-left:10px;">C. to use your best endeavours to execute all documents and do all acts both during and after your employment by us as may, in the opinion of the Employer, be necessary or desirable to vest the Employment IPRs in the Employer, to register them in the name of the Employer and to protect and maintain the Employment IPRs and the Employment Inventions;</li>
            <li style="margin:0;padding:0;margin-left:10px;">D. to give us all necessary assistance to enable us to enforce the Company's Intellectual Property Rights against third parties, to defend claims for infringement of third party Intellectual Property Rights and to apply for registration of Intellectual Property Rights, where appropriate throughout the world, and for the full term of those rights;</li>
            <li style="margin:0;padding:0;margin-left:10px;">E. not to attempt to register any Employment IPR nor patent any Employment Invention unless the Company requests that you do so;</li>
            <li style="margin:0;padding:0;margin-left:10px;">F. to keep confidential each Employment Invention unless the Company has consented to its disclosure in writing.</li>
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
        <ul style="margin:0;padding:0;">
            <li style="margin:0;padding:0;margin-left:10px;">A. are used primarily to further the business of the Company;</li>
            <li style="margin:0;padding:0;margin-left:10px;">B. are not used for inappropriate or unlawful purposes, such as accessing or circulating material containing nudity, pornography, racist terminology or other offensive material or for telephoning premium-rate lines;</li>
            <li style="margin:0;padding:0;margin-left:10px;">C. are used in compliance with the relevant policies and procedures of the Company;</li>
            <li style="margin:0;padding:0;margin-left:10px;">D. have sufficient capacity for the needs of the business.</li>
        </ul>
        <p>By signing this contract you understand and agree that the content of your communications and access to the Company's systems can be accessed and hereby consent to any such interception, monitoring, viewing and/or recording taking place.</p>

        <h3>18. Collective Agreements</h3>
        <p>There are no collective agreements affecting your terms and conditions of employment.</p>

        <h3>19. Amendment to Terms and Conditions</h3>
        <p>The Company is entitled to make reasonable changes to any of the terms of your employment as it deems appropriate. The Company will notify you in writing of any change before the date it comes into force.</p>

        <h3>20. Prevention of Unfair Competition</h3>
        <p>In this clause, "Restricted Client" means any client of the Company or any business in which the Company has a shareholding with whom you have had dealings while in employment, within a period of 12 months preceding the date of termination of your employment. "Restricted Employee" means any senior or professional employee of the Company of Manager level or above with whom you worked in the 12 months preceding the date of termination of your employment (whether alone or in conjunction with others who are members of a team in which you worked) where the departure of that employee is intended to be for your benefit, or the benefit of your new employer or any other organisation carrying out business in competition with the Company.</p>
        <p>You hereby agree that for a period of 12 months following the date of termination of your employment you shall not without the written consent of the Company (such consent not to be unreasonably withheld) in the United Kingdom:</p>
        <ul style="margin:0;padding:0;">
            <li style="margin:0;padding:0;margin-left:10px;">A. Render services similar to those services offered by the Company of a kind performed by you during the 12 months preceding the date of termination of your employment to any Restricted Client except where such services are rendered as an employee of the Restricted Client;</li>
            <li style="margin:0;padding:0;margin-left:10px;">B. Solicit the business of, or endeavour to solicit the business of, any Restricted Client in competition with the business of or services offered by the Company;</li>
            <li style="margin:0;padding:0;margin-left:10px;">C. Solicit, entice away, or endeavour to entice away or assist any third party to solicit, entice away or endeavour to entice away from the Company, any Restricted Employee.</li>
            <li style="margin:0;padding:0;margin-left:10px;">D. The above provisions are considered reasonable and necessary to protect legitimate business interests of the Company.</li>
        </ul>

        <h3>21. Governing Law</h3>
        <p>This Agreement shall be governed by and interpreted according to the law of England and Wales and all disputes arising under the Agreement (including non-contractual disputes or claims) shall be subject to the exclusive jurisdiction of the English and Welsh courts.</p>

        <h3>22. Third Party Rights</h3>
        <p>No provision of this contract shall be enforceable by any person who is not a party to it pursuant to the Contracts (Rights of Third Parties) Act 1999.</p>


        <p>IN WITNESS WHEREOF, the parties have executed this Agreement as of the day and year first above written.
        </p>


        <br>
        <div class="agreement-section">
            <!-- Signature Section -->
            <div class="agreement-section">
                {{-- <h3>Name: {{ $customer->first_name }} {{ $customer->last_name }}</h3> --}}
                {{-- <h4>Signature Date:{{ \Carbon\Carbon::createFromFormat('d/m/Y', $today)->format('d-F-Y') }}</h4> --}}

                <p>Employee Name: <b>{{ $customer }}</b></p>
                <br><br>
                <img src="{{ storage_path('app/public/' . $SIGFILE) }}" style="width: 299.25px; height: 106.7px">
                <b>
                    <p>Date: <b> {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</b></p>
                </b>
                <br>
                <p><b>Neguinho Motors Ltd</b></p>
                <br>
                <div>
                    <img src="{{ secure_asset('https://neguinhomotors.co.uk/img/sig/sign-t.png') }}"
                        style="height: 54px;">
                </div>

                <p>Date: <b>{{ \Carbon\Carbon::parse($date)->format('d F Y') }} </b></p>

                <br>
                <p>This Non-Disclosure Agreement is designed to be legally binding and enforceable under UK law,
                    including
                    provisions that allow for police involvement and court enforcement in cases of breach.</p>
            </div>
        </div>

    </div>

    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $size = 10;
                $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
                $y = 15;
                $x = 520;
                $pdf->text($x, $y, $pageText, $font, $size);
            ');
        }
    </script>

    <div class="footer">

        {{-- Page <span class="page-num"></span> of <span class="page-count"></span> --}}

    </div>

</body>

</html>
