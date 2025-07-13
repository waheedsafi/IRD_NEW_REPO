   <div class="page"></div>
   <br>
   {!! '<tocentry content="Goals" level="3" />' !!}
   <h5>
       Goals: </h5>
   {{ $goals }}
   <br>
   {!! '<tocentry content="Objectives" level="3" />' !!}
   <h5>

       Objectives: </h5>
   {{ $objectives }}
   <br>
   {!! '<tocentry content="Expected Outcomes" level="3" />' !!}
   <h5>
       Expected Outcomes: </h5>
   {{ $expected_outcomes }}
   <br>
   {!! '<tocentry content="Expected Impact:" level="3" />' !!}
   <h5>
       Expected Impact: </h5>
   {{ $expected_impact }}
   <br>
   </p>

   {{-- Article Ten --}}
   <p class="content-text" id="mou-section-3">

   <div class="article-title">

       <h3>
           Article Ten</h3>

   </div>
   {!! '<tocentry content="Project Description" level="3" />' !!}
   <h4>
       Project Description : </h4>

   {{-- {{ $backgroud_experince }} --}}

   <table width="100%" style="border-collapse: collapse; font-size: 13px; margin-top: 20px;">
       <!-- Subject -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black; width: 35%;">Subject
           </td>
           <td style="padding: 6px; border: 1px solid black">{{ $subject }}</td>
       </tr>

       <!-- Goals -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Goals</td>
           <td style="padding: 6px; border: 1px solid black">{{ $goals }}</td>
       </tr>

       <!-- Objectives -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Objectives</td>
           <td style="padding: 6px; border: 1px solid black">{{ $objectives }}</td>
       </tr>

       <!-- Main Activities -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Main Activities</td>
           <td style="padding: 6px; border: 1px solid black">{!! nl2br(e($activities)) !!}</td>
       </tr>

       <!-- Implementing Organization -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Implementing
               Organization</td>
           <td style="padding: 6px; border: 1px solid black">{{ $implementing_org }}</td>
       </tr>

       <!-- Funded by -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Funded by</td>
           <td style="padding: 6px; border: 1px solid black">{{ $funder }}</td>
       </tr>

       <!-- Total Budget -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Total Budget</td>
           <td style="padding: 6px; border: 1px solid black">{{ $budget }}</td>
       </tr>

       <!-- Duration (Nested with border) -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; vertical-align: top; border: 1px solid black">
               Duration</td>
           <td style="padding: 0px; border: 1px solid black">
               <table width="100%" style="border-collapse: collapse; font-size: 13px; border: 1px solid black">
                   <tr>
                       <td style="padding: 6px; border: 1px solid black width: 30%;"><strong>Start
                               Date:</strong></td>
                       <td style="padding: 6px; border: 1px solid black">{{ $start_date }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 6px; border: 1px solid black;"><strong>Finish Date:</strong></td>
                       <td style="padding: 6px; border: 1px solid black;">{{ $end_date }}</td>
                   </tr>
               </table>
           </td>
       </tr>

       <!-- MOU Date -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Date of signing of MOU
           </td>
           <td style="padding: 6px; border: 1px solid black">{{ $mou_date }}</td>
       </tr>

       <!-- Location -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Location</td>
           <td style="padding: 6px; border: 1px solid black">{{ $location }}</td>
       </tr>

       <!-- Provinces covered -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Provinces covered</td>
           <td style="padding: 6px; border: 1px solid black">{{ $provinces }}</td>
       </tr>

       <!-- Areas covered -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Areas, Villages or
               Districts covered</td>
           <td style="padding: 6px; border: 1px solid black">{{ $areas }}</td>
       </tr>

       <!-- Beneficiaries -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">No. of Direct
               Beneficiaries</td>
           <td style="padding: 6px; border: 1px solid black">{{ $direct_beneficiaries }}</td>
       </tr>
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">No. of Indirect
               Beneficiaries</td>
           <td style="padding: 6px; border: 1px solid black">{{ $indirect_beneficiaries }}</td>
       </tr>

       <!-- Project Staff (Nested with border) -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; vertical-align: top; border: 1px solid black">
               Project Staff (with provincial staff)</td>
           <td style="padding: 1px; border: 1px solid black">
               <table width="100%" style="border-collapse: collapse; font-size: 13px; border: 1px solid black">
                   <tr>
                       <td style="padding: 6px; border: 1px solid black width: 40%;"><strong>Organizational
                               structure:</strong></td>
                       <td style="padding: 6px; border: 1px solid black">{{ $org_structure }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 6px; border: 1px solid black"><strong>No. and type of Health
                               Staff:</strong></td>
                       <td style="padding: 6px; border: 1px solid black">{{ $health_staff }}</td>
                   </tr>
                   <tr>
                       <td style="padding: 6px; border: 1px solid black"><strong>No. & type of Administrative
                               Staff:</strong></td>
                       <td style="padding: 6px; border: 1px solid black">{{ $admin_staff }}</td>
                   </tr>
               </table>
           </td>
       </tr>

       <!-- Action Plan -->
       <tr>
           <td style="background-color: #d9e2f3; padding: 6px; border: 1px solid black">Action Plan</td>
           <td style="padding: 6px; border: 1px solid black">{!! nl2br(e($action_plan)) !!}</td>
       </tr>
   </table>

   <br>
   {!! '<tocentry content="Contact Information" level="3" />' !!}
   <h5>
       Contact Information:</h5><br>
   {{ $provision_health_service }}

   <table width="100%" style="border-collapse: collapse; font-size: 13px; margin-top: 20px;">
       <!-- Subject -->
       <thead>
           <tr style="background-color: #f2f2f2;">
               <th style="border: 1px solid black; padding: 8px;">CEO/Person in charge of the <br> Organization
                   in Afghanistan </th>
               <th style="border: 1px solid black; padding: 8px;">Focal Person for the current project</th>
           </tr>
       </thead>
       <tr>
           <td style=" padding: 6px; border: 1px solid black; ">
               {{ $ngo_director_contact }}
           </td>
           <td style="padding: 6px; border: 1px solid black">{{ $project_focal_point_contact }}</td>
       </tr>

       <!-- Goals -->
       <tr>
           <td style="padding: 6px; border: 1px solid black">
               {{ $ngo_director_contact }}</td>
           <td style="padding: 6px; border: 1px solid black">{{ $project_focal_point_contact }}</td>
       </tr>
   </table>
   </p>

   <p class="content-text" id="mou-section-3">

   <div class="article-title">

       <h3>
           Article Eleven</h3>

   </div>
   {!! '<tocentry content="Responsibilities and Commitments of the Organization" level="3" />' !!}
   <h4>
       Responsibilities and Commitments of the Organization: </h4>

   1. The Organization has to present a detailed work plan to be agreed in the project’s contract (xxxxxxxx),
   prior to commencing the activities mentioned in this MoU.
   <br>
   2. The Organization is fully responsible and committed for proper implementation of the project work plan
   and
   activities as outlined in the MoU. The Organization agrees to perform the necessary activities transparently
   and in an accountable manner. Furthermore, the Organization is committed to make available sufficient
   financial, human and other resources for the implementation of the project’s activities and fulfillment of
   its goals and objectives.
   <br>
   3. The Organization is liable to start and implement all activities relevant to the current project in
   accordance with the policies and strategies of the MoPH and all laws and regulations of the country.
   <br>
   4. The Organization will provide the financial and program activities reports on quarterly, semiannually and
   annually, according to the health system information and standards to relevant departments of the MoPH and
   Provincial Health Directorates of {{ $project_provinces }} provinces.
   <br>
   5. The Organization agrees that MoPH has the right to monitor and evaluate the all activities of project &
   implementation plan of the Organization’s project in relevant provinces in grant period.
   <br>
   6. The Organization agrees to deliver all project activities free of cost to the beneficiaries.

   <br>
   7. The Organization will perform all project activities in coordination with Provincial Health
   Directorate of
   {{ $project_provinces }} provinces and will participate in the relevant coordination meetings and utilize
   the guidance and
   cooperation of the Directorate when necessary. <br>

   8. According to the policies of Ministry of Public Health, the outcomes of the completed project will be
   shared with the MoPH and the provincial health directorate at the end of the project.
   <br>
   9. The Organization shall get the documents of its medical doctors, nurses, midwives and health personnel
   certified by the concerned Provincial Health Directorate, Health legislation Directorate and the General
   Directorate of Human Resources of MoPH, otherwise the responsibility lies with the Organization.
   <br>
   10. The Organization is responsible for facilitating the round-trip facilities and other necessities for the
   Ministry of public Health team during the supervision and monitoring the projects of that organization.

   </p>

   {{-- article twelve  --}}
   <p class="content-text" id="mou-section-3">

   <div class="article-title">

       <h3>
           Article Twelve</h3>

   </div>
   {!! '<tocentry content="Responsibilities and Commitments of the MoPH" level="3" />' !!}
   <h4>
       Responsibilities and Commitments of the MoPH: </h4>
   1. Wherever required, the MoPH will confirm that the Youth Health and Development Organization operates its
   activities in coordination with MoPH as a known entity.
   <br>
   2. The relevant Provincial Health Directorate has the responsibility to monitor and evaluate the quality of
   the project and activities of the Organization during the MoU period.
   <br>
   3. After finalization of MOU and relevant documents the MoPH will be informed the provincial health
   directorate of {{ $project_provinces }} provinces to provide the required support to the Organization,
   regarding the correct
   implementation of projects activities.
   <br>
   4. The MoPH has the right to terminate the MoU at any time during project implementation phase for its
   non-compliance with any of the above provisions of the MoU.
   </p>

   {{-- article thirteen --}}
   <p class="content-text" id="mou-section-3">

   <div class="article-title">

       <h3>
           Article Thirteen</h3>

   </div>
   {!! '<tocentry content="Responsibilities and Commitments of both Parties" level="3" />' !!}
   <h4>
       Responsibilities and Commitments of both Parties: </h4>
   1. The MoPH and the Organization will respect the principles of equity, transparency and non-discrimination
   on the basis of ethnicity, gender, religion and any other related factors.
   <br>
   <p style="color: red">
       2. In case the organization cannot fulfill its commitments, the Ministry of Public Health will pursue
       the
       unilateral termination of the MOU and the issue will follows through Justice and litigation.
   </p>

   <br>
   3. Both parties commit to work together for providing high quality services, to help in development and
   implementation of policy and programs, which aim to improve the quality of health services in Afghanistan.
   <br>
   4. Both parties will share technical information without any limitation or restriction through meetings,
   formal discussions and other mechanisms.
   <br>
   5. Both MOPH/ and {{ $ngo_name }} parties agree to establish joint monitoring plan and agree to oversee
   the project
   progress, achievements according to the project contract and have jointly decision for implementation
   challenges in field the MOPH will lead this joint mission.
   <br>
   6. The Parties commit to protecting the data, information, and documentation resulting from carrying out
   work
   under the framework of this MoU.
   <br>
   7. Information provided by one Party to the other, in the context of this MoU, shall be treated as
   confidential, unless the information is publicly available and/or is already known. The Parties shall take
   all reasonable measures to keep the information confidential and shall only use the information for the
   purpose for which it was provided.
   <br>
   8. By completion and shut down of the Organization`s activities and mission in Afghanistan, in accordance
   with the NGOs laws, all materials, equipment and facilities of the Organization will be handed over to the
   MoPH/government of Afghanistan.
   <br>
   <p style="color:red">
       9. Disputes and internal conflicts will be resolved through discussion with Provincial Health
       Coordination
       Committee (PHCC) or central MoPH representatives. This MOU is valid for three years until the completion
       of
       the project .The Ministry of Public Health has the right to unilaterally amend and terminate it. MOU can
       be
       extended if the Organization can fulfill all its commitments and is acceptable to the MOPH.
   </p>

   </p>

   <p class="content-text" id="mou-section-3">

   <div class="article-title">

       <h3>
           Article Fourteen</h3>

   </div>
   {!! '<tocentry content="Implementation and Cancellation Date of the MoU" level="3" />' !!}
   <h4>
       Implementation and Cancellation Date of the MoU:
   </h4>
   This MoU is applicable and valid from the date of signing by the representative of MoPH and representative
   of the Organization. By singing this MoU, both parties hereby accept all articles and agree with all its
   obligations.
   <br>
   This memorandum can be modified or cancelled according to written agreement by both parties on the basis of
   convincing reasons. Two original copies of this MoU are signed, each party will have a copy.
   <br>
   The duration of this MoU from the June 2021 to end of December 2021.
   <br>
   This MoU is signed by the following authorized persons.

   </p>

   {{-- ds --}}

   <div class="page"></div>

   <div class="sing-page">
       <div class="irddirector">
           <b> On behalf of the Ministry of Public Health</b>
           <br>
           {{ $ird_director }}<br>
           Mowlawi Noor Jalal “Jalali”
           Acting Minister of Public Health

           Signature
           ____________________________________
           Date ( / / ):

           Director of International Relations<br><br><br>
           Signature:................................
       </div>
       <br>

       <div class="ngodirector">
           <b>On behalf of the Organization</b> <br>
           <br>
           Signature
           ____________________________________
           Date ( / / ):
       </div>
   </div>

   </body>

   </html>
