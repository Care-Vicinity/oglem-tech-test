## Instructions

The goal for this task is to:
- Using Laravel, create functionality that returns JSON response(s) to provide data in order to produce the following design
- Create an endpoint that receives a JSON payload to add a new bill to the database.
- Create a command that will automatically assign all bills that currently not assigned and in stage 'submitted' to a user. A user should only have a maximum of 3 bills assigned to them.

<img src="https://trilogy-care-public-hosted.s3.ap-southeast-2.amazonaws.com/other/design.png" alt="Page design">

Run the migration and seeder to populate the database with dummy data: 
```
php artisan migrate --seed
```

- In order to copmlete this task, you will need to create or modify relevant files in this project. You can determine the best approach to structure your code, JSON and any other files in order to achieve the stated goals and demonstrate your understanding of the Laravel framework.
- This task should take 2 hours to complete.
- Bonus points for tests. 
- Please note, you are not required to do any front end work, but if you wish to impress and have time, then feel free to add a very basic front end implementation. The focus should be on structure rather than visuals. We have setup this project to use Vue3, TailwindsCSS and InertiaJS, which is our current tech stack. The purpose of this is to save you time configuring the project. If you are familiar with this stack, then please utilise it, using ```php artisan serve``` and ```npm run dev```. This site will be accessible at localhost:8000. Otherwise feel free to use something you are more familiar with is you want to do show your front end skills.

### How to submit:

Create a PR or create a zip of the files and email: liams@trilogycare.com.au


### Notes:

- Feel free to leave a readme or description in your PR on your approach to the challenge, or any additional commentary that you think would assist us in understanding your process.
- While we expect the task should only take 2 hours to complete, you are free to spend as much time as you'd like if there's some extra sauce you want to share with us. Opting to not take the extra time will *not* hamper your eligibility for the role.
- Please specify how much time you chose to use with your submission
- You might find it easiest to install Laravel Sail if you don't have a native PHP install on your dev machine.

## Peters Notes

### Time taken, 4 Hours

I took a little bit longer as I haven't seen an embedded Vue project inside Laravel like this.

In projects I normally see a client code base and an API code base. Generally a SPA doing AJAX Cors requests onto the API.

I spent a bit of time reading the docs and playing with Inertia. It looks like a really nice way of passing state between the two systems. I don't think I have it fully understood yet and will be very interested to see how it scales out.

I did create 2 sets of end points, one was inertia and the other was JSON as requested as inertia load isn't strictly JSON.

Then I added the Command
```
php artisan app:assign-bills-to-users
```

The only major thing with this one is I round-robined the submission of bills onto the users. I figured this was a good idea if each user was required to approve the Bills so not just dumping them all onto one user. I also adheared to the max of 3 bills, reporting if there are no users who can take bills.

Finally I started doing some testing, my dev environment is wsl running apache, mysql etc, this was creating issues with the phpunit testing on the feature testing as I have multiple projects running on the same Apache.

So I decided to chuck sail in, I avoid putting Docker on most of the time because my laptop has only 16gb of ram and it chugs when im running wsl + docker + everything else. I started the adventure of installing docker without breaking any of my other dev setup.

Docker went and tried to murder my laptop. Eventually I got it sorted after windows finally rebooted 3 times.

The feature tests then decided to work on the get and post requests.

I don't feel like there was a heap of good things to test but I wrote some to test the creation and fetching of Bills.

### Issues
- No Foreign Keys, was thinking of fixing but multiple Laravel projects I've seen, seem to not care?
- Bill => BillStage was being generated with a 0-7 range meaning some were invalid joins.